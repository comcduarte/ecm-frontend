<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Digits;

class AmendmentFieldset extends Fieldset implements InputFilterProviderInterface
{
    public $amounts = [
        'ORIGINAL_SUM',
        'PREVIOUS_CHANGE',
        'PREVIOUS_SUM',
        'CURRENT_CHANGE',
        'CURRENT_SUM',
    ];
    
    public function __construct(string $name = 'AmendmentFieldset', array $options = [])
    {
        parent::__construct($name, $options);
        
        $this->setAttribute('class', 'form-control');
    }
    
    public function init()
    {
        $this->add([
            'name' => 'CHANGES',
            'type' => Textarea::class,
            'attributes' => [
                'id' => 'CHANGES',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => "List of Changes to Contract",
            ]
        ]);
        
        $this->add([
            'name' => 'AMENDMENT_NUM',
            'type' => Text::class,
            'attributes' => [
                'id' => 'AMENDMENT_NUM',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => "Amendment Number",
            ]
        ]);
        
        foreach ($this->amounts as $name) {
            $this->add([
                'name' => $name,
                'type' => Text::class,
                'attributes' => [
                    'id' => $name,
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => $name,
                ]
            ]);
        }
        
    }
    
    public function getInputFilterSpecification()
    {
        $spec = [];
        
        foreach ($this->amounts as $name) {
            $spec[$name] = [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ];
        }
        
        $spec['CHANGES'] = [
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ];
        
        $spec['AMENDMENT_NUM'] = [
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                ['name' => Digits::class]
            ],
        ];
        
        return $spec;
    }
}