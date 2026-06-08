<?php
declare(strict_types = 1);
namespace Frontend\Contract\Handler\Route;

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
use comcduarte\Box\API\Resource\BoxSign\BoxSignRequest;
class PostRouteSignCancelHandler implements RequestHandlerInterface
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
        $access_token = $this->accessTokenService->getAccessToken();
        
        $sign_request_id = $request->getAttribute('uuid');
        
        $request = new BoxSignRequest($access_token);
        $result = $request->get_box_sign_request_by_id($sign_request_id);
        
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        $result = $request->cancel_box_sign_request($sign_request_id);
        if ($result instanceof ClientError) {
            throw new ClientErrorException($result->message);
        }
        
        $uri = $this->router->generateUri('workflow::dashboard', ['action' => 'index']);
        return new RedirectResponse($uri);
    }
}