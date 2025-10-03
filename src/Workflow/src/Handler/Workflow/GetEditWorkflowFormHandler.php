<?php

declare(strict_types=1);

namespace Frontend\Workflow\Handler\Workflow;

use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Frontend\App\Exception\NotFoundException;
use Frontend\Workflow\Form\EditWorkflowForm;
use Frontend\Workflow\Service\WorkflowServiceInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetEditWorkflowFormHandler implements RequestHandlerInterface
{
    #[Inject(
        WorkflowServiceInterface::class,
        RouterInterface::class,
        TemplateRendererInterface::class,
        FlashMessengerInterface::class,
        EditWorkflowForm::class,
    )]
    public function __construct(
        protected WorkflowServiceInterface $workflowService,
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected FlashMessengerInterface $messenger,
        protected EditWorkflowForm $editWorkflowForm,
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

        $this->editWorkflowForm
            ->setAttribute(
                'action',
                $this->router->generateUri('workflow::edit-workflow', ['uuid' => $workflow->getUuid()->toString()])
            )
            ->bind($workflow);

        return new HtmlResponse(
            $this->template->render('workflow::edit-workflow-form', [
                'form' => $this->editWorkflowForm->prepare(),
                'workflow' => $workflow,
            ])
        );
    }
}
