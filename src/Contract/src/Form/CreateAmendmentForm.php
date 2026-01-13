<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\Size;

class CreateAmendmentForm extends AbstractContractForm
{
    public function __construct(
        ?string $name = null,
        array $options = [],
        ) {
            parent::__construct($name, $options);
            
            $this->init();
            
            $this->setAttribute('id', 'u-cgs-form');
            $this->setAttribute('class', 'row g-3 needs-validation');
            $this->setAttribute('novalidate', 'novalidate');
    }
    
    public function init(): void
    {
        parent::init();
        
        $this->add([
            'name' => 'FILE',
            'type' => File::class,
            'attributes' => [
                'id' => 'FILE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Upload File',
            ],
        ]);
        
        $this->add([
            'name' => 'CONTRACT_ID',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CONTRACT_ID',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Original Contract Folder ID',
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
                'label' => 'Amendment Name',
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
            'CONTRACT_ID' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
            'FILE' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => Extension::class,
                        'options' => [
                            'extension' => ['docx','pdf','doc'],
                        ],
                    ],
                    [
                        'name' => Size::class,
                        'options' => [
                            'max' => '10MB',
                        ],
                    ]
                ],
            ],
        ];
        
        $merged = array_replace_recursive($parentSpec, $childSpec);
        return $merged;
    }
}