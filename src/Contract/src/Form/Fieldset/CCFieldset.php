<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputFilterProviderInterface;

class CCFieldset extends Fieldset implements InputFilterProviderInterface
{
    public $contractTypes = ['STATE','FED','COOP'];
    
    public function __construct(string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
    }
    
    public function init()
    {
        
        foreach ($this->contractTypes as $type) {
            $this->add([
                'name' => $type . '_CONTRACT_NUM',
                'type' => Text::class,
                'attributes' => [
                    'id' => $type . '_CONTRACT_NUM',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => $type . " Contract Number",
                ]
            ]);
            
            $this->add([
                'name' => $type . '_CONTRACT_EXP',
                'type' => Text::class,
                'attributes' => [
                    'id' => $type . '_CONTRACT_EXP',
                    'class' => 'form-control',
                ],
                'options' => [
                    'label' => $type . " Contract Expiration Date",
                ]
            ]);
        }
        
        $this->add([
            'name' => 'COOP_CONTRACT_NAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'COOP_CONTRACT_NAME',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => "Cooperative Contract Name",
            ]
        ]);
    }
    
    public function getInputFilterSpecification()
    {
        $spec = [];
        
        foreach ($this->contractTypes as $type) {
            $name = $type . '_CONTRACT_NUM';
            $spec[$name] = [
                'required' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
            ];
            
            $name = $type . '_CONTRACT_EXP';
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
                            'format' => 'Y-m-d'
                        ]
                    ],
                ],
            ];
        }
        
        $name = 'COOP_CONTRACT_NAME';
        $spec[$name] = [
            'required' => false,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ];
        
        return $spec;
    }
}