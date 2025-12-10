<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\CreateUCGSInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type CreateUCGSDataType from CreateUCGSInputFilter
 * @extends AbstractForm<CreateUCGSDataType>
 */
class CreateUCGSForm extends AbstractForm
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

        $this->setAttribute('id', 'u-cgs-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');

        $this->inputFilter = new CreateUCGSInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        $this->add([
            'name' => 'DOCTYPE',
            'type' => Select::class,
            'attributes' => [
                'id' => 'DOCTYPE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Document Type',
                'value_options' => [
                    'contract' => 'Contract',
                    'legal-opinion' => 'Legal Opinion',
                    'amendment' => 'Amendment',
                ],
            ],
        ]);
        
        $this->add([
            'name' => 'ENTITY',
            'type' => Radio::class,
            'attributes' => [
                'class' => 'form-check-input',
            ],
            'options' => [
                'value_options' => [
                    'city' => 'City of Middletown',
                    'library' => 'Russell Library Company',
                    'both' => 'City of Middletown & Russell Library Company',
                ],
            ],
        ]);
        
        
        /**
         * Entity
         */
        $this->add([
            'name' => 'ENTITY_NAME',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'ENTITY_NAME',
                'class' => 'form-control',
                'value' => 'City of Middletown',
            ],
            'options' => [
                'label' => 'Entity Name',
            ],
        ]);
        
        $this->add([
            'name' => 'ENTITY_LOGO',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'ENTITY_LOGO',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Entity Logo',
            ],
        ]);
        
        $this->add([
            'name' => 'ENTITY_ALIAS',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'ENTITY_ALIAS',
                'class' => 'form-control',
                'value' => 'the City',
            ],
            'options' => [
                'label' => 'Entity Alias',
            ],
        ]);
        
        $this->add([
            'name' => 'START_DATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'START_DATE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Start Date',
            ],
        ]);
        
        $this->add([
            'name' => 'CONTRACT_END_DATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CONTRACT_END_DATE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'End Date',
            ],
        ]);
        
        $this->add([
            'name' => 'CONTRACT_AMOUNT',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CONTRACT_AMOUNT',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Contract Amount',
            ],
        ]);
        
        $this->add([
            'name' => 'PROJECT_NAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'PROJECT_NAME',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Project Name',
            ],
        ]);
        
        $this->add([
            'name' => 'DEPARTMENT',
            'type' => Select::class,
            'attributes' => [
                'id' => 'DEPARTMENT',
                'class' => 'form-control',
                'onchange' => 'document.getElementById("parent").value=document.getElementById("DEPARTMENT").value',
            ],
            'options' => [
                'label' => 'Department',
            ],
        ]);
        
        $this->add([
            'name' => 'parent',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'parent',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Department Queue Folder ID',
            ],
        ]);
        
        
        $this->add([
            'name' => 'TYPE',
            'type' => Select::class,
            'attributes' => [
                'id' => 'TYPE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Document Template',
            ],
        ],['priority' => 100]);
        
        $this->add(
            (new Csrf('createUCGSCsrf'))
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
