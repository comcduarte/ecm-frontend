<?php
declare(strict_types=1);

namespace Frontend\App\Form\View\Helper;

use Laminas\Form\View\Helper\FormElementErrors;
use Psr\Container\ContainerInterface;

class FormElementErrorsFactory
{
    public function __invoke(ContainerInterface $container): FormElementErrors
    {
        $helper = new FormElementErrors();
        
        $config = $container->get('config');
        if (isset($config['view_helpers']['config']['form_element_errors'])) {
            $configHelper = $config['view_helpers']['config']['form_element_errors'];
            if (isset($configHelper['message_open_format'])) {
                $helper->setMessageOpenFormat(
                    $configHelper['message_open_format']
                    );
            }
            if (isset($configHelper['message_separator_string'])) {
                $helper->setMessageSeparatorString(
                    $configHelper['message_separator_string']
                    );
            }
            if (isset($configHelper['message_close_string'])) {
                $helper->setMessageCloseString(
                    $configHelper['message_close_string']
                    );
            }
            // Attributes
            if (isset($configHelper['attributes']) && is_array($configHelper['attributes'])) {
                $helper->setAttributes($configHelper['attributes']);
            }
        }
        
        return $helper;
    }
}