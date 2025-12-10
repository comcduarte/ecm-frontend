<?php

declare(strict_types=1);

namespace Frontend\Workflow\Middleware;

use Dot\DependencyInjection\Attribute\Inject;
use Frontend\Workflow\Service\WorkflowServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WorkflowMiddleware implements MiddlewareInterface
{
    #[Inject(
        WorkflowServiceInterface::class,
    )]
    public function __construct(
        protected WorkflowServiceInterface $workflowService,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        // add logic here

        return $handler->handle($request);
    }
}
