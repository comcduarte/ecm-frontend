<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
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
use Throwable;

class PostDeleteTemplateHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        DeleteTemplateForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected TemplateServiceInterface $templateService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected DeleteTemplateForm $deleteTemplateForm,
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

        $this->deleteTemplateForm->setAttribute(
            'action',
            $this->router->generateUri('template::delete-template', ['uuid' => $template->getUuid()->toString()])
        );

        try {
            $data = (array) $request->getParsedBody();
            $this->deleteTemplateForm->setData($data);
            if ($this->deleteTemplateForm->isValid()) {
                $this->templateService->deleteTemplate($template);
                $this->messenger->addSuccess(Message::TEMPLATE_DELETED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('template::delete-template-form', [
                    'form' => $this->deleteTemplateForm->prepare(),
                    'template' => $template,
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->messenger->addError(Message::AN_ERROR_OCCURRED);
            $this->logger->err('Delete Template', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new EmptyResponse(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
