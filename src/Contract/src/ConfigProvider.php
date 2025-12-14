<?php

declare(strict_types=1);

namespace Frontend\Contract;

use Dot\DependencyInjection\Factory\AttributedServiceFactory;
use Frontend\Contract\Form\CreateCommentForm;
use Frontend\Contract\Form\CreateDepartmentHeadCertificationForm;
use Frontend\Contract\Form\CreateUCGSForm;
use Frontend\Contract\Form\CreateUCLTSForm;
use Frontend\Contract\Form\CreateUCLaborForm;
use Frontend\Contract\Form\DeleteContractForm;
use Frontend\Contract\Form\EditContractForm;
use Frontend\Contract\Form\UploadFileForm;
use Frontend\Contract\Form\Factory\CreateUCGSFormFactory;
use Frontend\Contract\Form\Factory\CreateUCLaborFormFactory;
use Frontend\Contract\Form\Factory\UploadFileFormFactory;
use Frontend\Contract\Form\Fieldset\DateFieldset;
use Frontend\Contract\Form\Fieldset\SignatureFieldset;
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
use Frontend\Contract\Handler\Document\GetViewDocumentHandler;
use Frontend\Contract\Handler\Document\PostCreateCommentHandler;
use Frontend\Contract\Handler\Document\PostUploadFileHandler;
use Frontend\Contract\Handler\Route\PostRouteContractHandler;
use Frontend\Contract\Handler\UCGS\GetCreateUCGSFormHandler;
use Frontend\Contract\Handler\UCGS\PostCreateUCGSHandler;
use Frontend\Contract\Handler\UCLTS\GetCreateUCLTSFormHandler;
use Frontend\Contract\Handler\UCLTS\PostCreateUCLTSHandler;
use Frontend\Contract\Middleware\MetadataCorrectionMiddleware;
use Frontend\Contract\Middleware\NotificationMiddleware;
use Frontend\Contract\Service\ContractService;
use Frontend\Contract\Service\ContractServiceInterface;
use Laminas\Form\ElementFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
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
            'form_elements' => $this->getFormElements(),
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
                GetSelectContractHandler::class     => AttributedServiceFactory::class,
                GetCreateContractFormHandler::class => AttributedServiceFactory::class,
                PostCreateContractHandler::class    => AttributedServiceFactory::class,
                GetDeleteContractFormHandler::class => AttributedServiceFactory::class,
                PostDeleteContractHandler::class    => AttributedServiceFactory::class,
                GetEditContractFormHandler::class   => AttributedServiceFactory::class,
                PostEditContractHandler::class      => AttributedServiceFactory::class,
                GetListContractHandler::class       => AttributedServiceFactory::class,
                GetViewContractHandler::class       => AttributedServiceFactory::class,
                GetImportContractHandler::class     => AttributedServiceFactory::class,
                
                GetViewDocumentHandler::class       => AttributedServiceFactory::class,
                PostCreateCommentHandler::class     => AttributedServiceFactory::class,
                PostUploadFileHandler::class        => AttributedServiceFactory::class,
                PostImportContractHandler::class    => AttributedServiceFactory::class,
                
                CreateCommentForm::class            => ElementFactory::class,
                UploadFileForm::class               => UploadFileFormFactory::class,
                
                //-- Custom Forms --//
                CreateUCLaborForm::class            => CreateUCLaborFormFactory::class,
                CreateDepartmentHeadCertificationForm::class => ElementFactory::class,
                
                CreateUCGSForm::class   => CreateUCGSFormFactory::class,
                GetCreateUCGSFormHandler::class => AttributedServiceFactory::class,
                PostCreateUCGSHandler::class => AttributedServiceFactory::class,
                
                CreateUCLTSForm::class => ElementFactory::class,
                GetCreateUCLTSFormHandler::class => AttributedServiceFactory::class,
                PostCreateUCLTSHandler::class   => AttributedServiceFactory::class,
                
                PostRouteContractHandler::class     => AttributedServiceFactory::class,
                
                DeleteContractForm::class           => ElementFactory::class,
                EditContractForm::class             => ElementFactory::class,
                
                ContractService::class => AttributedServiceFactory::class,
                
                NotificationMiddleware::class => AttributedServiceFactory::class,
                MetadataCorrectionMiddleware::class => AttributedServiceFactory::class,
            ],
            'aliases'    => [
                ContractServiceInterface::class => ContractService::class,
            ],
        ];
    }

    private function getFormElements(): array
    {
        return [
            'aliases' => [
                'date_fieldset'             => DateFieldset::class,
            ],
            'factories' => [
                DateFieldset::class                 => InvokableFactory::class,
                SignatureFieldset::class            => ElementFactory::class,
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
                'document' => [__DIR__ . '/../templates/document'],
                'document-partial' => [__DIR__ . '/../templates/document/partial'],
                'notifications' => [__DIR__ . '/../templates/notifications'],
            ],
        ];
    }
}
