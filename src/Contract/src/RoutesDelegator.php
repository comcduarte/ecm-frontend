<?php

declare(strict_types=1);

namespace Frontend\Contract;

use Core\Contract\ConfigProvider;
use Dot\Router\RouteCollectorInterface;
use Frontend\Contract\Handler\Contract\GetCreateContractFormHandler;
use Frontend\Contract\Handler\Contract\GetDeleteContractFormHandler;
use Frontend\Contract\Handler\Contract\GetEditContractFormHandler;
use Frontend\Contract\Handler\Contract\GetListContractHandler;
use Frontend\Contract\Handler\Contract\GetViewContractHandler;
use Frontend\Contract\Handler\Contract\PostCreateContractHandler;
use Frontend\Contract\Handler\Contract\PostDeleteContractHandler;
use Frontend\Contract\Handler\Contract\PostEditContractHandler;
use Frontend\Contract\Handler\Document\GetViewDocumentHandler;
use Frontend\Contract\Handler\Route\PostRouteContractHandler;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Frontend\Contract\Handler\Document\PostCreateCommentHandler;
use Frontend\Contract\Handler\Document\PostUploadFileHandler;

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
        $box_id = ConfigProvider::REGEXP_BOX_ID;

        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $container->get(RouteCollectorInterface::class);

        $routeCollector->group('/contract')
            ->get('/create', GetCreateContractFormHandler::class, 'contract::create-contract-form')
            ->post('/create', PostCreateContractHandler::class, 'contract::create-contract')
            
            ->get('/delete/' . $uuid, GetDeleteContractFormHandler::class, 'contract::delete-contract-form')
            ->post('/delete/' . $uuid, PostDeleteContractHandler::class, 'contract::delete-contract')
            
            ->get('/edit/' . $uuid, GetEditContractFormHandler::class, 'contract::edit-contract-form')
            ->post('/edit/' . $uuid, PostEditContractHandler::class, 'contract::edit-contract')
            
            ->get('/list', GetListContractHandler::class, 'contract::list-contract')
            ->get('/view/' . $box_id, GetViewContractHandler::class, 'contract::view-contract-form');

        $routeCollector->group('/route')
            ->get('/legal/' . $box_id , PostRouteContractHandler::class, 'route::legal')
            ->get('/risk/' . $box_id , PostRouteContractHandler::class, 'route::risk')
            ->get('/purchasing/' . $box_id , PostRouteContractHandler::class, 'route::purchasing')
            ->get('/mayor/' . $box_id , PostRouteContractHandler::class, 'route::mayor')
            ->get('/vendor/' . $box_id , PostRouteContractHandler::class, 'route::vendor')
            ->get('/reject/' . $box_id , PostRouteContractHandler::class, 'route::reject');
            
        $routeCollector->group('/document')
            ->get('/view/' . $box_id, GetViewDocumentHandler::class, 'document::view-document')
            ->post('/view/' . $box_id, PostCreateCommentHandler::class, 'document::create-comment')
            ->post('/upload/' . $box_id, PostUploadFileHandler::class, 'document::upload-file')
        ;
        
        return $callback();
    }
}
