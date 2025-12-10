<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Factory;

use Core\Contract\Enum\QueueFolderEnum;
use Frontend\Contract\Form\UploadFileForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class UploadFileFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $form = new UploadFileForm();
        
        foreach (QueueFolderEnum::cases() as $dept) {
            $options['value_options'][$dept->value] = $dept->name;
        }
        $form->get('DEPARTMENT')->setOptions($options);
        
        return $form;
    }
}