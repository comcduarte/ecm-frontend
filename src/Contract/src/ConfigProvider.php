<?php

declare(strict_types=1);

namespace Frontend\Contract;

use Dot\DependencyInjection\Factory\AttributedServiceFactory;
use Frontend\Contract\Form\CreateContractForm;
use Frontend\Contract\Form\DeleteContractForm;
use Frontend\Contract\Form\EditContractForm;
use Frontend\Contract\Handler\Contract\GetCreateContractFormHandler;
use Frontend\Contract\Handler\Contract\GetDeleteContractFormHandler;
use Frontend\Contract\Handler\Contract\GetEditContractFormHandler;
use Frontend\Contract\Handler\Contract\GetListContractHandler;
use Frontend\Contract\Handler\Contract\GetViewContractHandler;
use Frontend\Contract\Handler\Contract\PostCreateContractHandler;
use Frontend\Contract\Handler\Contract\PostDeleteContractHandler;
use Frontend\Contract\Handler\Contract\PostEditContractHandler;
use Frontend\Contract\Service\ContractService;
use Frontend\Contract\Service\ContractServiceInterface;
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
                GetCreateContractFormHandler::class => AttributedServiceFactory::class,
                PostCreateContractHandler::class => AttributedServiceFactory::class,
                GetDeleteContractFormHandler::class => AttributedServiceFactory::class,
                PostDeleteContractHandler::class => AttributedServiceFactory::class,
                GetEditContractFormHandler::class => AttributedServiceFactory::class,
                PostEditContractHandler::class => AttributedServiceFactory::class,
                GetListContractHandler::class => AttributedServiceFactory::class,
                GetViewContractHandler::class => AttributedServiceFactory::class,
                CreateContractForm::class => ElementFactory::class,
                DeleteContractForm::class => ElementFactory::class,
                EditContractForm::class => ElementFactory::class,
                ContractService::class => AttributedServiceFactory::class,
            ],
            'aliases'    => [
                ContractServiceInterface::class => ContractService::class,
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
                'contract' => [__DIR__ . '/../templates/contract'],
            ],
        ];
    }
}
