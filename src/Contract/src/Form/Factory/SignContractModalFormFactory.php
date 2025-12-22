<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Frontend\Contract\Form\SignContractModalForm;

class SignContractModalFormFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $form = new SignContractModalForm();
        $form->num_emails = 2;
        $form->init();
        return $form;
    }
}