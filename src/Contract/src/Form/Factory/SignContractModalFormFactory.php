<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Factory;

use Frontend\Contract\Form\SignContractModalForm;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class SignContractModalFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $form = new SignContractModalForm();
        return $form;
    }
}