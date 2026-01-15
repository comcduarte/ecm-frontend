<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Factory;

use Core\Contract\Enum\QueueFolderEnum;
use Frontend\Contract\Form\CreateAmendmentForm;
use Frontend\Template\Service\TemplateServiceInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CreateAmendmentFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $form = new CreateAmendmentForm();
        
        foreach (QueueFolderEnum::cases() as $dept) {
            $options['value_options'][$dept->value] = $dept->name;
        }
        $options['empty_option'] = 'Choose Department...';
        $form->get('DEPARTMENT')->setOptions($options);

        $templateService = $container->get(TemplateServiceInterface::class);
        $templates = $templateService->getTemplates();
        foreach ($templates as $template)
        {
            if ($template['name'] == '2025 Uniform Contract Amendment.docx') {
                $form->get('TYPE')->setAttribute('value', $template['id']);
            }
        }
        
        return $form;
    }
}