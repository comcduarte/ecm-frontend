<?php
declare(strict_types = 1);
namespace Frontend\Contract\Handler\Contract;

use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetSelectContractHandler implements RequestHandlerInterface
{

    #[Inject(RouterInterface::class, TemplateRendererInterface::class)]
    public function __construct(protected RouterInterface $router, protected TemplateRendererInterface $template)
    {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->template->render('contract::select-contract', [
            
        ]));
    }
}