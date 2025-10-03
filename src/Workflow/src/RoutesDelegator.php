<?php

declare(strict_types=1);

namespace Frontend\Workflow;

use Core\App\ConfigProvider;
use Dot\Router\RouteCollectorInterface;
use Frontend\Workflow\Handler\Workflow\GetCreateWorkflowFormHandler;
use Frontend\Workflow\Handler\Workflow\GetDeleteWorkflowFormHandler;
use Frontend\Workflow\Handler\Workflow\GetEditWorkflowFormHandler;
use Frontend\Workflow\Handler\Workflow\GetListWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\GetViewWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\PostCreateWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\PostDeleteWorkflowHandler;
use Frontend\Workflow\Handler\Workflow\PostEditWorkflowHandler;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RoutesDelegator
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback,
    ): Application {
        $uuid = ConfigProvider::REGEXP_UUID;

        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $container->get(RouteCollectorInterface::class);

        $routeCollector
            ->get('/create-workflow', GetCreateWorkflowFormHandler::class, 'workflow::create-workflow-form')
            ->post('/create-workflow', PostCreateWorkflowHandler::class, 'workflow::create-workflow')
            ->get('/delete-workflow/' . $uuid, GetDeleteWorkflowFormHandler::class, 'workflow::delete-workflow-form')
            ->post('/delete-workflow/' . $uuid, PostDeleteWorkflowHandler::class, 'workflow::delete-workflow')
            ->get('/edit-workflow/' . $uuid, GetEditWorkflowFormHandler::class, 'workflow::edit-workflow-form')
            ->post('/edit-workflow/' . $uuid, PostEditWorkflowHandler::class, 'workflow::edit-workflow')
            ->get('/list-workflow', GetListWorkflowHandler::class, 'workflow::list-workflow')
            ->get('/view-workflow/' . $uuid, GetViewWorkflowHandler::class, 'workflow::view-workflow-form');

        return $callback();
    }
}
