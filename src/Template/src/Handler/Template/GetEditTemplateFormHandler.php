<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Template\Form\EditTemplateForm;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetEditTemplateFormHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        EditTemplateForm::class,
    )]
    public function __construct(
        protected TemplateServiceInterface $templateService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected EditTemplateForm $editTemplateForm,
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

        $this->editTemplateForm
            ->setAttribute(
                'action',
                $this->router->generateUri('template::edit-template', ['uuid' => $template->getUuid()->toString()])
            )
            ->bind($template);

        return new HtmlResponse(
            $this->template->render('template::edit-template-form', [
                'form' => $this->editTemplateForm->prepare(),
                'template' => $template,
            ])
        );
    }
}
