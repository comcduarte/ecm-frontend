<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\UCLTS;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Contract\Form\CreateUCLTSForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateUCLTSFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateUCLTSForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateUCLTSForm $createUCLTSForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createUCLTSForm
            ->setAttribute('action', $this->router->generateUri('contract::post-uclts-form'));

        return new HtmlResponse(
            $this->template->render('contract::create-contract-form', [
                'form' => $this->createUCLTSForm->prepare(),
            ])
        );
    }
}
