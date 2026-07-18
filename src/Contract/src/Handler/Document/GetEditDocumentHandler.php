<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Document;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Frontend\App\Service\AccessTokenService;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use comcduarte\Box\API\Resource\File;
use comcduarte\Box\API\Resource\ClientError;
use comcduarte\Box\API\Exception\ClientErrorException;

class GetEditDocumentHandler implements RequestHandlerInterface
{
    #[Inject(
        AccessTokenService::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        RouterInterface::class,
        ContractServiceInterface::class,
        AuthenticationServiceInterface::class,
        )]
        public function __construct(
            protected AccessTokenService $accessTokenService,
            protected TemplateRendererInterface $template,
            protected FlashMessengerInterface $messenger,
            protected RouterInterface $router,
            protected ContractServiceInterface $contractService,
            protected AuthenticationServiceInterface $authenticationService,
            ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $file_id = $request->getAttribute('id');
        
        $file = new File($this->accessTokenService->getAccessToken());
        $file->getSharedLink()->getPermissions()->can_edit = true;
        $file->getSharedLink()->getPermissions()->can_upload = true;
        $result = $file->add_shared_link_to_file($file_id);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        $uri = $file->shared_link->url;
        
        return new RedirectResponse($uri);
    }
}