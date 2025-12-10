<?php

declare(strict_types=1);

namespace Frontend\Contract\Handler\CC;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\ConflictException;
use Frontend\Contract\Form\CreateCCForm;
use Frontend\Contract\Service\CCServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostCreateCCHandler implements RequestHandlerInterface
{
    #[Inject(
        CCServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateCCForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected CCServiceInterface $cCService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateCCForm $createCCForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createCCForm
            ->setAttribute('action', $this->router->generateUri('c-c::create-c-c'));

        try {
            $data = (array) $request->getParsedBody();
            $this->createCCForm->setData($data);
            if ($this->createCCForm->isValid()) {
                $data = $this->createCCForm->getData();
                $this->cCService->saveCC($data);
                $this->messenger->addSuccess(Message::C_C_CREATED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('c-c::create-c-c-form', [
                    'form' => $this->createCCForm->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (ConflictException $exception) {
            return new HtmlResponse(
                $this->template->render('c-c::create-c-c-form', [
                    'form'     => $this->createCCForm->prepare(),
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->logger->err('Create CC', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new HtmlResponse(
                $this->template->render('c-c::create-c-c-form', [
                    'form'     => $this->createCCForm->prepare(),
                    'messages' => [
                        'error' => Message::AN_ERROR_OCCURRED,
                    ],
                ]),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
