<?php
declare(strict_types=1);

namespace Frontend\Contract\Handler\Route;

use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PostRouteSignHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
    )]
    public function __construct(
        protected RouterInterface $router,
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $this->router->generateUri('workflow::dashboard', ['action' => 'index']);
        return new RedirectResponse($uri);
    }
}