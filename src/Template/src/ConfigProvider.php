<?php

declare(strict_types=1);

namespace Frontend\Template;

use Dot\DependencyInjection\Factory\AttributedServiceFactory;
use Frontend\Template\Form\CreateTemplateForm;
use Frontend\Template\Handler\TemplatePageHandler;
use Frontend\Template\Service\TemplateService;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\Form\ElementFactory;
use Mezzio\Application;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(), 
        ];
    }

    private function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [RoutesDelegator::class],
            ],
            'factories' => [
                TemplatePageHandler::class => AttributedServiceFactory::class,
//                 GetCreateTemplateFormHandler::class => AttributedServiceFactory::class,
//                 PostCreateTemplateHandler::class => AttributedServiceFactory::class,
//                 GetDeleteTemplateFormHandler::class => AttributedServiceFactory::class,
//                 PostDeleteTemplateHandler::class => AttributedServiceFactory::class,
//                 GetEditTemplateFormHandler::class => AttributedServiceFactory::class,
//                 PostEditTemplateHandler::class => AttributedServiceFactory::class,
//                 GetListTemplateHandler::class => AttributedServiceFactory::class,
//                 GetViewTemplateHandler::class => AttributedServiceFactory::class,
                CreateTemplateForm::class => ElementFactory::class,
//                 DeleteTemplateForm::class => ElementFactory::class,
//                 EditTemplateForm::class => ElementFactory::class,
                TemplateService::class => AttributedServiceFactory::class,
            ],
            'aliases'    => [
                TemplateServiceInterface::class => TemplateService::class,
            ],
        ];
    }

    private function getTemplates(): array
    {
        return [
            'paths' => [
                'template' => [__DIR__ . '/../templates/template'],
            ],
        ];
    }
}
