<?php

declare(strict_types=1);

namespace Frontend\Contract;

use Core\Contract\ConfigProvider;
use Dot\Router\RouteCollectorInterface;
use Frontend\Contract\Handler\Amendment\GetCreateAmendmentFormHandler;
use Frontend\Contract\Handler\Amendment\PostCreateAmendmentFormHandler;
use Frontend\Contract\Handler\CC\GetCreateCCFormHandler;
use Frontend\Contract\Handler\CC\PostCreateCCHandler;
use Frontend\Contract\Handler\Contract\GetCreateContractFormHandler;
use Frontend\Contract\Handler\Contract\GetDeleteContractFormHandler;
use Frontend\Contract\Handler\Contract\GetEditContractFormHandler;
use Frontend\Contract\Handler\Contract\GetImportContractHandler;
use Frontend\Contract\Handler\Contract\GetListContractHandler;
use Frontend\Contract\Handler\Contract\GetSelectContractHandler;
use Frontend\Contract\Handler\Contract\GetViewContractHandler;
use Frontend\Contract\Handler\Contract\PostCreateContractHandler;
use Frontend\Contract\Handler\Contract\PostDeleteContractHandler;
use Frontend\Contract\Handler\Contract\PostEditContractHandler;
use Frontend\Contract\Handler\Contract\PostImportContractHandler;
use Frontend\Contract\Handler\Document\GetEditDocumentHandler;
use Frontend\Contract\Handler\Document\GetViewDocumentHandler;
use Frontend\Contract\Handler\Document\PostCreateCommentHandler;
use Frontend\Contract\Handler\Document\PostUploadFileHandler;
use Frontend\Contract\Handler\Route\PostRouteContractHandler;
use Frontend\Contract\Handler\Route\PostRouteSignCancelHandler;
use Frontend\Contract\Handler\Route\PostRouteSignHandler;
use Frontend\Contract\Handler\UCGS\GetCreateUCGSFormHandler;
use Frontend\Contract\Handler\UCGS\PostCreateUCGSHandler;
use Frontend\Contract\Handler\UCLTS\GetCreateUCLTSFormHandler;
use Frontend\Contract\Handler\UCLTS\PostCreateUCLTSHandler;
use Frontend\Contract\Middleware\MetadataCorrectionMiddleware;
use Frontend\Contract\Middleware\NotificationMiddleware;
use Frontend\Contract\Middleware\PostRouteContractMiddleware;
use Frontend\Contract\Middleware\PostRouteSignMiddleware;
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
        $box_id = ConfigProvider::REGEXP_BOX_ID;

        /** @var RouteCollectorInterface $routeCollector */
        $routeCollector = $container->get(RouteCollectorInterface::class);

        $routeCollector->group('/amendment')
            ->get('/create', GetCreateAmendmentFormHandler::class, 'amendment::get-create')
            ->post('/create', PostCreateAmendmentFormHandler::class, 'amendment::post-create');
        
        $routeCollector->group('/cc')
            ->get('/create', GetCreateCCFormHandler::class, 'cc::get-create')
            ->post('/create', PostCreateCCHandler::class, 'cc::post-create');
            
        $routeCollector->group('/contract')
            ->get('/select', GetSelectContractHandler::class, 'contract::select-contract')
            
            ->get('/create/uclts', GetCreateUCLTSFormHandler::class, 'contract::create-uclts-form')
            ->post('/post/uclts', PostCreateUCLTSHandler::class, 'contract::post-uclts-form')
            
            ->get('/create/ucgs', GetCreateUCGSFormHandler::class, 'contract::create-ucgs-form')
            ->post('/post/ucgs', PostCreateUCGSHandler::class, 'contract::post-ucgs-form')
            
            ->get('/create/{form}', GetCreateContractFormHandler::class, 'contract::create-contract-form')
            ->post('/create', PostCreateContractHandler::class, 'contract::create-contract')
            
            ->get('/import', GetImportContractHandler::class, 'contract::import-contract-form')
            ->post('/import', PostImportContractHandler::class, 'contract::import-contract')
            
            ->get('/delete/' . $box_id, GetDeleteContractFormHandler::class, 'contract::delete-contract-form')
            ->post('/delete/' . $box_id, PostDeleteContractHandler::class, 'contract::delete-contract')
            
            ->get('/edit/' . $uuid, GetEditContractFormHandler::class, 'contract::edit-contract-form')
            ->post('/edit/' . $uuid, PostEditContractHandler::class, 'contract::edit-contract')
            
            ->get('/list', GetListContractHandler::class, 'contract::list-contract')
            ->get('/view/' . $box_id, [MetadataCorrectionMiddleware::class, GetViewContractHandler::class], 'contract::view-contract-form');

        $routeCollector->group('/route')->setMiddleware([PostRouteContractMiddleware::class, NotificationMiddleware::class])
            ->get('/dept/' . $box_id, PostRouteContractHandler::class, 'route::dept')
            ->get('/legal/' . $box_id , PostRouteContractHandler::class, 'route::legal')
            ->get('/risk/' . $box_id , PostRouteContractHandler::class, 'route::risk')
            ->get('/purchasing/' . $box_id , PostRouteContractHandler::class, 'route::purchasing')
            ->get('/mayor/' . $box_id , PostRouteContractHandler::class, 'route::mayor')
            ->get('/vendor/' . $box_id , PostRouteContractHandler::class, 'route::vendor')
            ->get('/reject/' . $box_id , PostRouteContractHandler::class, 'route::reject');
        
        $routeCollector->group('/sign')->setMiddleware([PostRouteSignMiddleware::class, NotificationMiddleware::class])
            ->post('/contract/{folder_id:[0-9-]*}/{file_id:[0-9-]*}', PostRouteSignHandler::class, 'route::sign')
        ;
        $routeCollector
            ->get('/cancel/sign/' . $box_id, PostRouteSignCancelHandler::class, 'route::sign-cancel');
        
        $routeCollector->group('/document')
            ->get('/edit/' . $box_id, GetEditDocumentHandler::class, 'document::edit-document')
            ->get('/view/' . $box_id, GetViewDocumentHandler::class, 'document::view-document')
            ->post('/view/' . $box_id, PostCreateCommentHandler::class, 'document::create-comment')
            ->post('/upload/' . $box_id, PostUploadFileHandler::class, 'document::upload-file')
        ;
        
        return $callback();
    }
}
