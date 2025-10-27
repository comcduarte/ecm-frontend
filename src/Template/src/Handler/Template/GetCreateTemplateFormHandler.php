<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Template\Form\CreateTemplateForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateTemplateFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateTemplateForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateTemplateForm $createTemplateForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createTemplateForm
            ->setAttribute('action', $this->router->generateUri('template::create-template'));

        return new HtmlResponse(
            $this->template->render('template::create-template-form', [
                'form' => $this->createTemplateForm->prepare(),
            ])
        );
    }
}
