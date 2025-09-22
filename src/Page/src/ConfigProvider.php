<?php

declare(strict_types=1);

namespace Frontend\Page;

use Dot\DependencyInjection\Factory\AttributedServiceFactory;
use Frontend\Page\Controller\PageController;
use Frontend\Page\Controller\WorkflowPageController;
use Frontend\Page\Service\PageService;
use Frontend\Page\Service\PageServiceInterface;
use Mezzio\Application;
use Frontend\Page\Controller\HomeController;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                HomeController::class => AttributedServiceFactory::class,
                PageController::class => AttributedServiceFactory::class,
                PageService::class    => AttributedServiceFactory::class,
                WorkflowPageController::class => AttributedServiceFactory::class,
            ],
            'aliases'    => [
                PageServiceInterface::class => PageService::class,
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'page' => [__DIR__ . '/../templates/page'],
                'workflow' => [__DIR__ . '/../templates/workflow'],
                'home' => [__DIR__ . '/../templates/home'],
            ],
        ];
    }
}
