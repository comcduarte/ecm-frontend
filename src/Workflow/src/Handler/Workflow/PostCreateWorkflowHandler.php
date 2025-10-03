<?php

declare(strict_types=1);

namespace Frontend\Workflow\Handler\Workflow;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\ConflictException;
use Frontend\Workflow\Form\CreateWorkflowForm;
use Frontend\Workflow\Service\WorkflowServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostCreateWorkflowHandler implements RequestHandlerInterface
{
    #[Inject(
        WorkflowServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        CreateWorkflowForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected WorkflowServiceInterface $workflowService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected CreateWorkflowForm $createWorkflowForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createWorkflowForm
            ->setAttribute('action', $this->router->generateUri('workflow::create-workflow'));

        try {
            $data = (array) $request->getParsedBody();
            $this->createWorkflowForm->setData($data);
            if ($this->createWorkflowForm->isValid()) {
                $data = $this->createWorkflowForm->getData();
                $this->workflowService->saveWorkflow($data);
                $this->messenger->addSuccess(Message::WORKFLOW_CREATED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('workflow::create-workflow-form', [
                    'form' => $this->createWorkflowForm->prepare(),
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (ConflictException $exception) {
            return new HtmlResponse(
                $this->template->render('workflow::create-workflow-form', [
                    'form'     => $this->createWorkflowForm->prepare(),
                    'messages' => [
                        'error' => $exception->getMessage(),
                    ],
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->logger->err('Create Workflow', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new HtmlResponse(
                $this->template->render('workflow::create-workflow-form', [
                    'form'     => $this->createWorkflowForm->prepare(),
                    'messages' => [
                        'error' => Message::AN_ERROR_OCCURRED,
                    ],
                ]),
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR
            );
        }
    }
}
