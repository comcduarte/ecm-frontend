<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\CreatePostUCLaborFormInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type CreatePostUCLaborFormDataType from CreatePostUCLaborFormInputFilter
 * @extends AbstractForm<CreatePostUCLaborFormDataType>
 */
class CreatePostUCLaborForm extends AbstractForm
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

        $this->setAttribute('id', 'post-uc-labor-form-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');

        $this->inputFilter = new CreatePostUCLaborFormInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        // add more form elements

        $this->add(
            (new Csrf('createPostUCLaborFormCsrf'))
                ->setOptions([
                    'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
                ])
                ->setAttribute('required', true)
        );
        $this->add(
            (new Submit('submit'))
                ->setAttribute('type', 'submit')
                ->setAttribute('value', 'Save')
                ->setAttribute('class', 'btn btn-primary btn-color btn-sm')
        );
    }
}
