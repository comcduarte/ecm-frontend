<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\CreateDepartmentHeadCertificationInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Textarea;

/**
 * @phpstan-import-type CreateDepartmentHeadCertificationDataType from CreateDepartmentHeadCertificationInputFilter
 * @extends AbstractForm<CreateDepartmentHeadCertificationDataType>
 */
class CreateDepartmentHeadCertificationForm extends AbstractForm
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

        $this->setAttribute('id', 'department-head-certification-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');

        $this->inputFilter = new CreateDepartmentHeadCertificationInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        $this->add([
            'name' => 'AGREEMENT_COST',
            'type' => Textarea::class,
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Set forth the cost of the Agreement, broken out by fiscal year.',
            ],
        ]);
        
        $this->add([
            'name' => 'SUFFICIENT_FUNDS',
            'type' => Radio::class,
            'attributes' => [
                'class' => 'form-check-input',
            ],
            'options' => [
                'value_options' => [
                    'yes' => 'Yes',
                    'no' => 'No',
                ],
                'label' => 'Does your department have sufficient unexpended and appropriated funds to cover the cost of this Agreement?',
            ],
        ]);
        
        $this->add([
            'name' => 'SPECIAL_TERMS',
            'type' => Textarea::class,
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Please list any special terms and conditions required by federal and state government funding sources.',
            ],
        ]);

        $this->add(
            (new Csrf('createDepartmentHeadCertificationCsrf'))
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
