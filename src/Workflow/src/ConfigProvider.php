<?php

declare(strict_types=1);

namespace Frontend\Workflow;

use Dot\DependencyInjection\Factory\AttributedServiceFactory;
use Frontend\Workflow\Form\CreateWorkflowForm;
use Frontend\Workflow\Form\DeleteWorkflowForm;
use Frontend\Workflow\Form\EditWorkflowForm;
use Frontend\Workflow\Handler\Workflow\GetCreateWorkflowFormHandler;
use Frontend\Workflow\Handler\Workflow\GetDeleteWorkflowFormHandler;
use Frontend\Workflow\Handler\Workflow\GetEditWorkflowFormHandler;
use Frontend\Workflow\Handler\Workflow\GetListWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\GetViewWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\PostCreateWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\PostDeleteWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\PostEditWorkflowHandler;
use Frontend\Workflow\Middleware\WorkflowMiddleware;
use Frontend\Workflow\Service\WorkflowService;
use Frontend\Workflow\Service\WorkflowServiceInterface;
use Laminas\Form\ElementFactory;
use Mezzio\Application;

/**
 * @phpstan-type ConfigType array{
 *      dependencies: DependenciesType,
 *      templates: TemplatesType,
 * }
 * @phpstan-type DependenciesType array{
 *      delegators: non-empty-array<class-string, array<class-string>>,
 *      factories: non-empty-array<class-string, class-string>,
 *      aliases: non-empty-array<class-string, class-string>,
 * }
 * @phpstan-type TemplatesType array{
 *      paths: non-empty-array<non-empty-string, non-empty-string[]>,
 * }
 */
class ConfigProvider
{
    /**
     * @return ConfigType
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * @return DependenciesType
     */
    private function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [RoutesDelegator::class],
            ],
            'factories' => [
                GetCreateWorkflowFormHandler::class => AttributedServiceFactory::class,
                PostCreateWorkflowHandler::class => AttributedServiceFactory::class,
                GetDeleteWorkflowFormHandler::class => AttributedServiceFactory::class,
                PostDeleteWorkflowHandler::class => AttributedServiceFactory::class,
                GetEditWorkflowFormHandler::class => AttributedServiceFactory::class,
                PostEditWorkflowHandler::class => AttributedServiceFactory::class,
                GetListWorkflowHandler::class => AttributedServiceFactory::class,
                GetViewWorkflowHandler::class => AttributedServiceFactory::class,
                CreateWorkflowForm::class => ElementFactory::class,
                DeleteWorkflowForm::class => ElementFactory::class,
                EditWorkflowForm::class => ElementFactory::class,
                WorkflowMiddleware::class => AttributedServiceFactory::class,
                WorkflowService::class => AttributedServiceFactory::class,
                Controller\WorkflowPageController::class => AttributedServiceFactory::class,
            ],
            'aliases'    => [
                WorkflowServiceInterface::class => WorkflowService::class,
            ],
        ];
    }

    /**
     * @return TemplatesType
     */
    private function getTemplates(): array
    {
        return [
            'paths' => [
                'workflow' => [__DIR__ . '/../templates/workflow'],
            ],
        ];
    }
}
