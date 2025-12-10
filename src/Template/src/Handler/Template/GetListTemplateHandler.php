<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetListTemplateHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateServiceInterface::class,
        TemplateRendererInterface::class,
    )]
    public function __construct(
        protected TemplateServiceInterface $templateService,
        protected TemplateRendererInterface $template,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        return new HtmlResponse(
            $this->template->render('template::list-template', [
                'pagination' => $this->templateService->getTemplates($request->getQueryParams()),
            ])
        );
    }
}
