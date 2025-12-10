<?php

declare(strict_types=1);

namespace Frontend\Template\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Template\InputFilter\DeleteTemplateInputFilter;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type DeleteTemplateDataType from DeleteTemplateInputFilter
 * @extends AbstractForm<DeleteTemplateDataType>
 */
class DeleteTemplateForm extends AbstractForm
{
    /**
     * @throws ExceptionInterface
     */
    public function __construct(
        ?string $name = null,
        array $options = [],
    ) {
        parent::__construct($name, $options);

        $this->init();

        $this->setAttribute('id', 'template-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');

        $this->inputFilter = new DeleteTemplateInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        // add more form elements

        $this->add(
            (new Checkbox('confirmation'))
                ->setCheckedValue('yes')
                ->setUncheckedValue('no')
                ->setAttribute('id', 'confirmation')
                ->setAttribute('class', 'form-check-input')
                ->setAttribute('required', true)
                ->setValue('no')
        );
        $this->add(
            (new Csrf('deleteTemplateCsrf'))
                ->setOptions([
                    'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
                ])
                ->setAttribute('required', true)
        );
        $this->add(
            (new Submit('submit'))
                ->setAttribute('type', 'submit')
                ->setAttribute('value', 'Delete')
                ->setAttribute('class', 'btn btn-primary btn-color btn-sm')
        );
    }
}
