<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Route;

use Core\Metadata\Instance\EcmApplication;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Form\SignContractModalForm;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Exception\ClientErrorException;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\File;
use comcduarte\Box\API\Resource\Folder;
use comcduarte\Box\API\Resource\BoxSign\BoxSignRequest;
use comcduarte\Box\API\Resource\BoxSign\BoxSigner;
use Throwable;

class PostRouteSignHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        AccessTokenService::class,
        SignContractModalForm::class,
        FlashMessengerInterface::class,
        ContractServiceInterface::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected AccessTokenService $accessTokenService,
        protected SignContractModalForm $form,
        protected FlashMessengerInterface $messenger,
        protected ContractServiceInterface $contractService,
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $source_files = [$request->getAttribute('file_id')];
        $folder_id = $request->getAttribute('folder_id');
        $access_token = $this->accessTokenService->getAccessToken();
        
        $parent_folder = new Folder($access_token);
        $parent_folder->get_folder_information($folder_id);
        
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
                $box_approver->email = 'developer@middletownct.gov';
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
            } else {
                throw new \Exception('Form was invalid.');
            }
            
            //-- Update Metadata --//            
            $folder_id = $parent_folder->getId();
            $scope = 'enterprise';
            $template_key = 'ecm-application';
            $data = [
                [
                    'op' => 'replace',
                    'path' => '/queue',
                    'value' => '',
                ],
            ];
            
            $metadata_instance = new EcmApplication($access_token);
            $result = $metadata_instance->update_metadata_instance_on_folder($folder_id, $scope, $template_key, $data);
            if ($result instanceof ClientError) {
                throw new ClientErrorException($result->message);
            }
            
            $this->messenger->addSuccess('Box Sign Request submitted successfully.');
        } catch (\Throwable $e) {
            $this->messenger->addError($e->getMessage());
        }
        
        $uri = $this->router->generateUri('workflow::dashboard', ['action' => 'index']);
        return new RedirectResponse($uri);
    }
}