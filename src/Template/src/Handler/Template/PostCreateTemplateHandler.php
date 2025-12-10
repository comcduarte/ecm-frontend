<?php

declare(strict_types=1);

namespace Frontend\Template\Handler\Template;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\ConflictException;
use Frontend\Template\Form\CreateTemplateForm;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostCreateTemplateHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateTemplateForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected TemplateServiceInterface $templateService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateTemplateForm $createTemplateForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createTemplateForm
            ->setAttribute('action', $this->router->generateUri('template::create-template'));

        try {
            $data = (array) $request->getParsedBody();
            $this->createTemplateForm->setData($data);
            if ($this->createTemplateForm->isValid()) {
                $data = $this->createTemplateForm->getData();
                $this->templateService->saveTemplate($data);
                $this->messenger->addSuccess(Message::TEMPLATE_CREATED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('template::create-template-form', [
                    'form' => $this->createTemplateForm->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (ConflictException $exception) {
            return new HtmlResponse(
                $this->template->render('template::create-template-form', [
                    'form'     => $this->createTemplateForm->prepare(),
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->logger->err('Create Template', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new HtmlResponse(
                $this->template->render('template::create-template-form', [
                    'form'     => $this->createTemplateForm->prepare(),
                    'messages' => [
                        'error' => Message::AN_ERROR_OCCURRED,
                    ],
                ]),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
