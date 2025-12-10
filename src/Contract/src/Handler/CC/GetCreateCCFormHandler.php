<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\CC;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\CreateCCForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateCCFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateCCForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateCCForm $createCCForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createCCForm
            ->setAttribute('action', $this->router->generateUri('c-c::create-c-c'));

        return new HtmlResponse(
            $this->template->render('c-c::create-c-c-form', [
                'form' => $this->createCCForm->prepare(),
            ])
        );
    }
}
