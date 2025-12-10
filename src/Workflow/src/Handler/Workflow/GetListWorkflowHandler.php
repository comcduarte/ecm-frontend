<?php

declare(strict_types=1);

namespace Frontend\Workflow\Handler\Workflow;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Workflow\Service\WorkflowServiceInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetListWorkflowHandler implements RequestHandlerInterface
{
    #[Inject(
        WorkflowServiceInterface::class,
        TemplateRendererInterface::class,
    )]
    public function __construct(
        protected WorkflowServiceInterface $workflowService,
        protected TemplateRendererInterface $template,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        return new HtmlResponse(
            $this->template->render('workflow::list-workflow', [
                'pagination' => $this->workflowService->getWorkflows($request->getQueryParams()),
            ])
        );
    }
}
