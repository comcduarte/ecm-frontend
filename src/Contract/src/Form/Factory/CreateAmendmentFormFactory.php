<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Factory;

use Core\Contract\Enum\QueueFolderEnum;
use Frontend\Contract\Form\CreateAmendmentForm;
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

        return $form;
    }
}