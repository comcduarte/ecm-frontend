<?php

declare(strict_types=1);

namespace Frontend\Workflow\Handler\Workflow;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
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

class GetDeleteWorkflowFormHandler implements RequestHandlerInterface
{
    #[Inject(
        WorkflowServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        DeleteWorkflowForm::class,
    )]
    public function __construct(
        protected WorkflowServiceInterface $workflowService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected DeleteWorkflowForm $deleteWorkflowForm,
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

        return new HtmlResponse(
            $this->template->render('workflow::delete-workflow-form', [
                'form' => $this->deleteWorkflowForm->prepare(),
                'workflow' => $workflow,
            ])
        );
    }
}
