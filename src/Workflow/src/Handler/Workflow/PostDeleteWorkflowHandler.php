<?php

declare(strict_types=1);

namespace Frontend\Workflow\Handler\Workflow;

use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Log\Logger;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Workflow\Form\DeleteWorkflowForm;
use Frontend\Workflow\Service\WorkflowServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class PostDeleteWorkflowHandler implements RequestHandlerInterface
{
    #[Inject(
        WorkflowServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        DeleteWorkflowForm::class,
        'dot-log.default_logger',
    )]
    public function __construct(
        protected WorkflowServiceInterface $workflowService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected DeleteWorkflowForm $deleteWorkflowForm,
        protected Logger $logger,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        try {
            $workflow = $this->workflowService->findWorkflow($request->getAttribute('uuid'));
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->deleteWorkflowForm->setAttribute(
            'action',
            $this->router->generateUri('workflow::delete-workflow', ['uuid' => $workflow->getUuid()->toString()])
        );

        try {
            $data = (array) $request->getParsedBody();
            $this->deleteWorkflowForm->setData($data);
            if ($this->deleteWorkflowForm->isValid()) {
                $this->workflowService->deleteWorkflow($workflow);
                $this->messenger->addSuccess(Message::WORKFLOW_DELETED);

                return new EmptyResponse(StatusCodeInterface::STATUS_CREATED);
            }

            return new HtmlResponse(
                $this->template->render('workflow::delete-workflow-form', [
                    'form' => $this->deleteWorkflowForm->prepare(),
                    'workflow' => $workflow,
                ]),
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY
            );
        } catch (Throwable $exception) {
            $this->messenger->addError(Message::AN_ERROR_OCCURRED);
            $this->logger->err('Delete Workflow', [
                'error' => $exception->getMessage(),
                'file'  => $exception->getFile(),
                'line'  => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return new EmptyResponse(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
