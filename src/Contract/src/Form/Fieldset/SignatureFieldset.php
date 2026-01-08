<?php
declare(strict_types = 1);
namespace Frontend\Contract\Form\Fieldset;

use Frontend\Contract\InputFilter\Input\TextInput;
use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;
use Laminas\InputFilter\InputFilterProviderInterface;

class SignatureFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct($name = 'SignatureFieldset', $options = [])
    {
        parent::__construct($name, $options);
        
        $this->setAttribute('class', 'form-control');
    }
    
    public function init(): void
    {
        $this->add([
            'name' => 'FNAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'FNAME',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'First Name'
            ]
        ]);
        
        $this->add([
            'name' => 'LNAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'LNAME',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Last Name'
            ]
        ]);
        
        $this->add([
            'name' => 'COMPANY',
            'type' => Text::class,
            'attributes' => [
                'id' => 'COMPANY',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Company'
            ]
        ]);
        
        $this->add([
            'name' => 'ADDRESS',
            'type' => Text::class,
            'attributes' => [
                'id' => 'ADDRESS',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Address'
            ]
        ]);
        
        $this->add([
            'name' => 'CITY',
            'type' => Text::class,
            'attributes' => [
                'id' => 'CITY',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'City'
            ]
        ]);

        $this->add([
            'name' => 'STATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'STATE',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'State'
            ]
        ]);
        
        $this->add([
            'name' => 'ZIP',
            'type' => Text::class,
            'attributes' => [
                'id' => 'ZIP',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => 'Postal Code'
            ]
        ]);

        $this->add([
            'name' => 'EMAIL',
            'type' => Text::class,
            'attributes' => [
                'id' => 'EMAIL',
                'class' => 'form-control',
                'required' => true,
            ],
            'options' => [
                'label' => ' Email Address'
            ]
        ]);
    }
    
    public function getInputFilterSpecification()
    {
        return [
            'FNAME' => new TextInput('FNAME', true),
            'LNAME' => new TextInput('LNAME', true),
            'COMPANY' => new TextInput('COMPANY', true),
            'ADDRESS' => new TextInput('ADDRESS', true),
            'CITY' => new TextInput('CITY', true),
            'STATE' => new TextInput('STATE', true),
            'ZIP' => new TextInput('ZIP', true),
            'EMAIL' => [
                'required' => true,
                'allow_empty' => false,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    ['name' => 'EmailAddress'],
                    ['name' => 'NotEmpty'],
                ],
            ],
        ];
    }
}