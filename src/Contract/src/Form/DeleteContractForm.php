<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\DeleteContractInputFilter;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type DeleteContractDataType from DeleteContractInputFilter
 * @extends AbstractForm<DeleteContractDataType>
 */
class DeleteContractForm extends AbstractForm
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

        $this->setAttribute('id', 'contract-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');

        $this->inputFilter = new DeleteContractInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        $this->add([
            'name' => 'confirmation',
            'type' => Checkbox::class,
            'attributes' => [
                'id' => 'confirmation',
                'class' => 'form-check-input',
            ],
            'options' => [
                'label' => 'Are you sure you want to delete this contract?',
            ],
        ]);
        $this->add(
            (new Csrf('deleteContractCsrf'))
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
