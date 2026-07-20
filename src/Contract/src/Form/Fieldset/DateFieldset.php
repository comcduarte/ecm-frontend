<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Date;

class DateFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(string $name = 'DateFieldset', array $options = [])
    {
        parent::__construct($name, $options);
        
        $this->setAttribute('class', 'form-control');
    }
    
    public function init(): void
    {
        $this->add([
            'name' => 'START_DATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'START_DATE',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Start Date'
            ]
        ]);
        
        $this->add([
            'name' => 'CONTRACT_END_DATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CONTACT_END_DATE',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'End Date'
            ]
        ]);
        
        $this->add([
            'name' => 'EXEC_DATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'EXEC_DATE',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Execution Date'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        $spec = [];
        
        $dates = ['START', 'CONTRACT_END', 'EXEC'];
        
        foreach ($dates as $prefix) {
            $name = $prefix . '_DATE';
            $spec[$name] = [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'], 
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    [
                        'name' => 'Date',
                        'options' => [
                            'format' => 'Y-m-d',
                            'messages' => [
                                Date::INVALID_DATE => 'The date must be in the format YYYY-MM-DD.',
                                Date::FALSEFORMAT => 'The date must be in the format YYYY-MM-DD.',
                            ],
                            
                        ]
                    ],
                ],
            ];
            
        }
        return $spec;
    }
}