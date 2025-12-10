<?php
declare(strict_types = 1);
namespace Frontend\Contract\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;

class SignatureFieldset extends Fieldset
{

    public function init(): void
    {
        $this->add([
            'name' => 'FNAME',
            'type' => Text::class,
            'attributes' => [
                'id' => 'FNAME',
                'class' => 'form-control'
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
                'class' => 'form-control'
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
                'class' => 'form-control'
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
                'class' => 'form-control'
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
                'class' => 'form-control'
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
                'class' => 'form-control'
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
                'class' => 'form-control'
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
                'class' => 'form-control'
            ],
            'options' => [
                'label' => ' Email Address'
            ]
        ]);
    }
}