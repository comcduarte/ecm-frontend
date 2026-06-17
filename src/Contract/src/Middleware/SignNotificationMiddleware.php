<?php
declare(strict_types = 1);
namespace Frontend\Contract\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Mail\Service\MailServiceInterface;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Authentication\AuthenticationServiceInterface;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Resource\MetadataInstance;
use Frontend\App\Service\AccessTokenService;

class SignNotificationMiddleware implements MiddlewareInterface
{
    #[Inject(
        TemplateRendererInterface::class,
        ContractServiceInterface::class,
        RouterInterface::class,
        MailServiceInterface::class,
        EntityManagerInterface::class,
        AuthenticationServiceInterface::class,
        FlashMessengerInterface::class,
        AccessTokenService::class,
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected ContractServiceInterface $contractService,
        protected RouterInterface $router,
        protected MailServiceInterface $mailService,
        protected EntityManagerInterface $entityManager,
        protected AuthenticationServiceInterface $authenticationService,
        protected FlashMessengerInterface $messenger,
        protected AccessTokenService $accessTokenService,
    ){}
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeResult = $this->router->match($request);
        $matchedParams = $routeResult->getMatchedParams();
//         $route_name = $routeResult->getMatchedRouteName();
//         $folder_id = $matchedParams['folder_id'];
        
        
        $access_token = $this->accessTokenService->getAccessToken();
        
        $metadata_instance = new MetadataInstance($access_token);
            $file_id = $matchedParams['file_id'];
            $scope = 'enterprise';
            $template_key = 'boxSign';
        try {
            $metadata_instance->get_metadata_instance_on_file($file_id, $scope, $template_key);
        } catch (ClientError $e) {
            $this->messenger->addError($e->message);
        }
        
        
//         $sign_request = new BoxSignRequest($access_token);
        
        /**
         * Construct Email
         * @var string $html
         */
        $html = $this->template->render('notifications::contract-routed',[
            'contract_number' => '1',
            'contract_name' => '1',
        ]);
        $this->mailService->getMessage()->addTo();
        $this->mailService->setBody($html);
        $this->mailService->setSubject(sprintf('[ECM] Contract: %s', 'Hello'));
        
        try {
            if (!$this->mailService->send()->isValid()) {
                throw new \Exception ('Email unable to send.');
            }
        } catch (\Exception $e) {
            $this->messenger->addError($e->getMessage());
            throw new \Exception ('Email unable to send.');
        }
        
        return $handler->handle($request);
    }
}