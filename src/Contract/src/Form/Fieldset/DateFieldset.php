<?php
declare(strict_types=1);

namespace Frontend\Contract\Form\Fieldset;

use Laminas\Form\Fieldset;
use Laminas\Form\Element\Text;

class DateFieldset extends Fieldset
{
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
            'name' => 'END_DATE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'END_DATE',
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
}