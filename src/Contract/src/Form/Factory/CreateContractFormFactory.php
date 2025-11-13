<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Factory;

use Core\Contract\Enum\QueueFolderEnum;
use Frontend\Contract\Form\CreateContractForm;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CreateContractFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $form = new CreateContractForm();
        
        $templateService = $container->get(TemplateServiceInterface::class);
        
        foreach (QueueFolderEnum::cases() as $dept) {
            $options['value_options'][$dept->value] = $dept->name;
        }
        $form->get('DEPARTMENT')->setOptions($options);
        
        $templates = $templateService->getTemplates();
        
        $options = [
            'value_options' => [
            ],
        ];
        foreach ($templates as $template) 
        {
            $options['value_options'][$template['id']] = $template['name'];
        }
        
        $form->get('TYPE')->setOptions($options);
        return $form;
    }
}