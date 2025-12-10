<?php
declare(strict_types = 1);
namespace Frontend\Contract\Form\Factory;

use Frontend\Contract\Form\CreateUCLaborForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class CreateUCLaborFormFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $form = new CreateUCLaborForm();
        return $form;
    }
}