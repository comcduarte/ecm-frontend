<?php

declare(strict_types=1);

namespace Frontend\Template\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Template\InputFilter\CreateTemplateInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type CreateTemplateDataType from CreateTemplateInputFilter
 * @extends AbstractForm<CreateTemplateDataType>
 */
class CreateTemplateForm extends AbstractForm
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

        $this->inputFilter = new CreateTemplateInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        // add more form elements

        $this->add(
            (new Csrf('createTemplateCsrf'))
                ->setOptions([
                    'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
                ])
                ->setAttribute('required', true)
        );
        
        $this->add(
            (new Text('test'))
                ->setAttribute('value', 'test')
        );
        
        $this->add(
            (new Submit('submit'))
                ->setAttribute('type', 'submit')
                ->setAttribute('value', 'Hello')
                ->setAttribute('class', 'btn btn-primary btn-color btn-sm')
        );
    }
}
