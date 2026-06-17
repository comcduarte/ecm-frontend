<?php
declare(strict_types = 1);
namespace Frontend\Contract\Middleware;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Form\SignContractModalForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Authentication\AuthenticationServiceInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\File;
use comcduarte\Box\API\Resource\Folder;
use comcduarte\Box\API\Resource\BoxSign\BoxSignRequest;
use comcduarte\Box\API\Resource\BoxSign\BoxSigner;

class PostRouteSignMiddleware implements MiddlewareInterface
{
    #[Inject(
        RouterInterface::class,
        FlashMessengerInterface::class,
        AccessTokenService::class,
        SignContractModalForm::class,
        ContractServiceInterface::class,
        AuthenticationServiceInterface::class,
        TemplateRendererInterface::class,
        'config',
    )]
    public function __construct(
        protected RouterInterface $router,
        protected FlashMessengerInterface $messenger,
        protected AccessTokenService $accessTokenService,
        protected SignContractModalForm $form,
        protected ContractServiceInterface $contractService,
        protected AuthenticationServiceInterface $authenticationService,
        protected TemplateRendererInterface $template,
        protected array $config,
    ){}
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $source_files = [$request->getAttribute('file_id')];
        $folder_id = $request->getAttribute('folder_id');
        $access_token = $this->accessTokenService->getAccessToken();
        
        $parent_folder = new Folder($access_token);
        $parent_folder->get_folder_information($folder_id);
        
        /**
         * Retrieve Contract
         * @var \Core\Contract\Entity\Contract $contract
         */
        $contract = $this->contractService->findContract($folder_id);
        
        /**
         * NotificationMiddleware
         */
        $notification = [];
        
        $notification['subject'] = sprintf('[ECM] Contract: %s', $contract->getContract_folder()->name);
        
        
        try {
            $data = $request->getParsedBody();
            $this->form->num_emails = $data['NUM_EMAILS'];
            $this->form->init();
            $this->form->setData($data);
            
            if ($this->form->isValid()) {
                $data = $this->form->getData();
                
                //-- Signers --//
                $i = 0;
                $signers = [];
                
                while (true) {
                    if (isset($data["EMAIL_$i"])) {
                        $box_signer = new BoxSigner();
                        $box_signer->role = 'signer';
                        $box_signer->email = $data["EMAIL_$i"];
                        $box_signer->order = $i;
                        $box_signer->suppress_notifications = true;
                        $box_signer->embed_url_external_user_id = sprintf('%s', $i);
                        
                        $signers[] = $box_signer;
                        $i++;
                    } else {
                        break;
                    }
                }
                
                $box_approver = new BoxSigner();
                $box_approver->role = 'approver';
                
                /**
                 * @TODO Make a form to allow the BoxSignRequest generator to add multiple emails
                 * and select their role from a dropdown.  Set defaults as appropriate.
                 */
                $box_approver->email = 'purchase@middletownct.gov';
                $box_approver->order = $i;
                $signers[] = $box_approver;
                
                //-- Source Files --//
                $source_files = [];
                
                $file = new File($access_token);
                $file->setId($request->getAttribute('file_id'));
                $source_files[] = $file;
                
                //-- Request --//
                $sign_request = new BoxSignRequest($access_token);
                $result = $sign_request->create_box_sign_request($parent_folder, $signers, $source_files);
                
                /**
                 * @var ClientError $result
                 */
                if ($result instanceof ClientError) {
                    throw new ClientErrorException($result->message);
                }
                
                /**
                 * Find logged in user
                 * @var \Frontend\User\Entity\UserIdentity $identity
                 */
                $identity = $this->authenticationService->getIdentity();
                $notification['logged_on_user'] = $identity->getIdentity();
                
                
                foreach ($sign_request->signers as $signer) {
                    if (isset($signer['embed_url'])) {
                        /**
                         * NotificationMiddleware
                         */
                        $notification['body'] = $this->template->render('notifications::box-sign',[
                            'fname' => $identity->getDetails()['firstName'],
                            'lname' => $identity->getDetails()['lastName'],
                            'email' => $identity->getIdentity(),
                            'url' => $signer['embed_url'],
                            'config' => $this->config,
                        ]);
                        $notification['users'] = [
                            $signer['email']
                        ];
                        
                        break;
                    }
                }
            } else {
                throw new \Exception('Form was invalid.');
            }
            
            $this->messenger->addSuccess('Box Sign Request submitted successfully.');
        } catch (\Throwable $e) {
            $this->messenger->addError($e->getMessage());
        }
        
        return $handler->handle($request->withAttribute('notification', $notification));
    }
}