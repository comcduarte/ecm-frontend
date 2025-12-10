<?php
declare(strict_types = 1);
namespace Frontend\Template;

use Core\App\ConfigProvider;
use Fig\Http\Message\RequestMethodInterface;
use Frontend\Template\Handler\TemplatePageHandler;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RoutesDelegator
{

    /**
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $uuid = ConfigProvider::REGEXP_UUID;

        $app = $callback();

        $app->route('/template[/{action}]', [
            TemplatePageHandler::class
        ], [
            RequestMethodInterface::METHOD_GET,
            RequestMethodInterface::METHOD_POST,
            RequestMethodInterface::METHOD_PUT
        ], 'template');

        // $routeCollector = $container->get(RouteCollectorInterface::class);

        // $routeCollector
        // ->get('/create-template', GetCreateTemplateFormHandler::class, 'template::create-template-form')
        // ->post('/create-template', PostCreateTemplateHandler::class, 'template::create-template')
        // ->get('/delete-template/' . $uuid, GetDeleteTemplateFormHandler::class, 'template::delete-template-form')
        // ->post('/delete-template/' . $uuid, PostDeleteTemplateHandler::class, 'template::delete-template')
        // ->get('/edit-template/' . $uuid, GetEditTemplateFormHandler::class, 'template::edit-template-form')
        // ->post('/edit-template/' . $uuid, PostEditTemplateHandler::class, 'template::edit-template')
        // ->get('/list-template', GetListTemplateHandler::class, 'template::list-template')
        // ->get('/view-template/' . $uuid, GetViewTemplateHandler::class, 'template::view-template-form');

        return $app;
    }
}
