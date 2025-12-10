<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Template\Form\DeleteTemplateForm;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetDeleteTemplateFormHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        DeleteTemplateForm::class,
    )]
    public function __construct(
        protected TemplateServiceInterface $templateService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected DeleteTemplateForm $deleteTemplateForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        try {
            $template = $this->templateService->findTemplate($request->getAttribute('uuid'));
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->deleteTemplateForm->setAttribute(
            'action',
            $this->router->generateUri('template::delete-template', ['uuid' => $template->getUuid()->toString()])
        );

        return new HtmlResponse(
            $this->template->render('template::delete-template-form', [
                'form' => $this->deleteTemplateForm->prepare(),
                'template' => $template,
            ])
        );
    }
}
