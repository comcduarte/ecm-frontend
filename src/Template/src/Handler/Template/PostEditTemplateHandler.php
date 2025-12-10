<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\BadRequestException;
use Frontend\App\Exception\ConflictException;
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
use Throwable;

class PostEditTemplateHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        EditTemplateForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected TemplateServiceInterface $templateService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected EditTemplateForm $editTemplateForm,
        protected Logger $logger,
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
            );

        try {
            $data = (array) $request->getParsedBody();
            $this->editTemplateForm->setData($data);
            if ($this->editTemplateForm->isValid()) {
                $data = (array) $this->editTemplateForm->getData();
                $this->templateService->saveTemplate($data, $template);
                $this->messenger->addSuccess(Message::TEMPLATE_UPDATED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('template::edit-template-form', [
                    'form' => $this->editTemplateForm->prepare(),
                    'template' => $template,
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (BadRequestException | ConflictException | NotFoundException $exception) {
            return new HtmlResponse(
                $this->template->render('template::edit-template-form', [
                    'form' => $this->editTemplateForm->prepare(),
                    'template' => $template,
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->messenger->addError(Message::AN_ERROR_OCCURRED);
            $this->logger->err('Update Template', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new EmptyResponse(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
