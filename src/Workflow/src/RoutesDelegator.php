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

        $routeCollector->group('/workflow')
            ->get('/create', GetCreateWorkflowFormHandler::class, 'workflow::create-form')
            ->post('/create', PostCreateWorkflowHandler::class, 'workflow::create')
            ->get('/delete/' . $uuid, GetDeleteWorkflowFormHandler::class, 'workflow::delete-form')
            ->post('/delete/' . $uuid, PostDeleteWorkflowHandler::class, 'workflow::delete')
            ->get('/edit/' . $uuid, GetEditWorkflowFormHandler::class, 'workflow::edit-form')
            ->post('/edit/' . $uuid, PostEditWorkflowHandler::class, 'workflow::edit')
            ->get('/list', GetListWorkflowHandler::class, 'workflow::list')
            ->get('/view/' . $uuid, GetViewWorkflowHandler::class, 'workflow::view-form')

            ->get('/dashboard', Controller\WorkflowPageController::class, 'workflow::dashboard');
        
        return $callback();
    }
}
