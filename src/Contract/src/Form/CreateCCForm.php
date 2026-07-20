<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\Contract\Form\Fieldset\CCFieldset;
use Frontend\Contract\Form\Fieldset\SignatureFieldset;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;

class CreateCCForm extends AbstractContractForm
{
    /**
     * @throws ExceptionInterface
     */
    public function __construct(
        ?string $name = null,
        array $options = [],
    ) {
        parent::__construct($name, $options);

        $this->setAttribute('id', 'c-c-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        parent::init();
        
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
            'name' => 'EXCESS_100K',
            'type' => Checkbox::class,
            'attributes' => [
                'class' => 'form-check-input',
            ],
            'options' => [
                'label' => 'Is this contract in excess of $100,000, or part of a project in which the total cost exceeds $100,000, or will the project exceed $100,000 with the inclusion of this contract?',
            ],
        ]);
        
        $this->add([
            'name' => 'GOV_USE',
            'type' => Checkbox::class,
            'attributes' => [
                'class' => 'form-check-input',
            ],
            'options' => [
                'label' => 'Is this contract allowed for the use of the city government or any of its departments and agencies',
            ],
        ]);
        
        $this->add([
            'name' => 'CC',
            'type' => CCFieldset::class,
            'attributes' => [
                'id' => 'CC',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Contract Information',
            ]
        ]);
        
        // Add the actual fieldset instance (not array config)
        $vendor = new SignatureFieldset('VENDOR');
        $vendor->setLabel('Vendor Information');
        $vendor->init();
        $this->add($vendor);
        
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
            'name' => 'TYPE',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'TYPE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Document Template',
            ],
        ],['priority' => 100]);
        
        $this->add([
            'name' => 'parent',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'parent',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Department Queue Folder ID'
            ]
        ]);
        
        $this->add([
            'name' => 'ENTITY',
            'type' => Hidden::class,
            'attributes' => [
                'class' => 'form-check-input',
                'value' => 'City of Middletown',
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        $parentSpec = parent::getInputFilterSpecification() ?: [];
        
        $childSpec = [
            'PROJECT_NAME' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
            'DEPARTMENT' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
            'parent' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
            'TYPE' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
            'EXCESS_100K' => [
                'required' => false,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
            'GOV_USE' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                    [
                        'name' => InArray::class,
                        'options' => [
                            'haystack' => ['1'],
                            'messages' => [
                                InArray::NOT_IN_ARRAY => 'You must check this box.',
                            ],
                        ],
                    ],
                ],
            ],
            'ENTITY' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
        ];
        
        $merged = array_replace_recursive($parentSpec, $childSpec);
        return $merged;
    }

}