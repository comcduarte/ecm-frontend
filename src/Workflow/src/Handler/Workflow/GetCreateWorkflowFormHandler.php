<?php

declare(strict_types=1);

namespace Frontend\Workflow\Handler\Workflow;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Workflow\Form\CreateWorkflowForm;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetCreateWorkflowFormHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        CreateWorkflowForm::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected CreateWorkflowForm $createWorkflowForm,
    ) {
    }

    public function handle(
        ServerRequestInterface $request,
    ): ResponseInterface {
        $this->createWorkflowForm
            ->setAttribute('action', $this->router->generateUri('workflow::create-workflow'));

        return new HtmlResponse(
            $this->template->render('workflow::create-workflow-form', [
                'form' => $this->createWorkflowForm->prepare(),
            ])
        );
    }
}
