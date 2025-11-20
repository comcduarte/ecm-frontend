<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\CreateContractInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type CreateContractDataType from CreateContractInputFilter
 * @extends AbstractForm<CreateContractDataType>
 */
class CreateContractForm extends AbstractForm
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

        $this->inputFilter = new CreateContractInputFilter();
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
        
        /**
         * Contract
         * @var \Laminas\Form\Fieldset $contract
         */
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
        
        
        /**
         * Exceeds 100K
         * @var \Laminas\Form\Fieldset $exceeds_100k
         */
//         $this->add([
//             'name' => 'EXCEEDS_100K',
//             'type' => Checkbox::class,
//             'attributes' => [
//                 'id' => 'EXCEEDS_100K',
//                 'class' => 'checkbox form-check-input checkbox-slider--b-flat',
//             ],
//             'options' => [
//                 'label' => 'Exceeds $100,000.00',
//                 'checked_value' => 'yes',
//                 'unchecked_value' => 'no',
//                 'use_hidden_element' => true,
//             ],
//         ]);
        
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
        
//         $this->add([
//             'name' => 'RESOLUTION_NUMBER',
//             'type' => Text::class,
//             'attributes' => [
//                 'id' => 'RESOLUTION_NUMBER',
//                 'class' => 'form-control',
//             ],
//             'options' => [
//                 'label' => 'Resolution Number',
//             ],
//         ]);
        
        /**
         * Vendor Information
         */
//         $this->add([
//             'name' => 'VENDOR_NAME',
//             'type' => Text::class,
//             'attributes' => [
//                 'id' => 'VENDOR_NAME',
//                 'class' => 'form-control',
//             ],
//             'options' => [
//                 'label' => 'Vendor Name',
//             ],
//         ]);
        
//         $this->add([
//             'name' => 'VENDOR_SIGNER',
//             'type' => Text::class,
//             'attributes' => [
//                 'id' => 'VENDOR_SIGNER',
//                 'class' => 'form-control',
//             ],
//             'options' => [
//                 'label' => 'Vendor Signer',
//             ],
//         ]);
        
//         $this->add([
//             'name' => 'VENDOR_SIGNER_EMAIL',
//             'type' => Text::class,
//             'attributes' => [
//                 'id' => 'VENDOR_SIGNER_EMAIL',
//                 'class' => 'form-control',
//             ],
//             'options' => [
//                 'label' => 'Vendor Signer Email',
//             ],
//         ]);
        
        /**
         * DEPARTMENT INFORMATION
         */
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
            (new Csrf('createContractCsrf'))
                ->setOptions([
                    'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
                ])
                ->setAttribute('required', true)
        );
        
        $this->add([
            'name' => 'SUBMIT',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary form-control mt-4',
                'id' => 'SUBMIT',
            ],
        ],['priority' => 0]);
    }
}
