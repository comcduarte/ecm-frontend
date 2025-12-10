<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\UCGS;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\CreateUCGSForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateUCGSFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateUCGSForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateUCGSForm $createUCGSForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createUCGSForm
            ->setAttribute('action', $this->router->generateUri('contract::post-ucgs-form'));

        return new HtmlResponse(
            $this->template->render('contract::create-contract-form', [
                'form' => $this->createUCGSForm->prepare(),
            ])
        );
    }
}
