<?php
declare(strict_types = 1);
namespace Frontend\Contract\Form;

use Frontend\Contract\Form\Fieldset\DateFieldset;
use Frontend\Contract\Form\Fieldset\SignatureFieldset;
use Frontend\Contract\InputFilter\CreateContractInputFilter;
use Laminas\Form\Fieldset;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Text;

class CreateUCLaborForm extends AbstractContractForm
{

    public function __construct(?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->inputFilter = new CreateContractInputFilter();
        $this->inputFilter->init();
    }
    
    public function init(): void
    {
        parent::init();
        
        $this->add([
            'name' => 'DATES',
            'type' => DateFieldset::class,
            'attributes' => [
                'id' => 'DATES',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Dates', 
            ]
        ]);
        
        $options = new Fieldset('INFO');
        $options->setOptions(['label' => 'Additional Information'])->setAttributes(['id' => 'INFO', 'class' => 'form-control mt-2 mb-2']);
        
        $options->add([
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
        
        $options->add([
            'name' => 'RESOLUTION_NUMBER',
            'type' => Text::class,
            'attributes' => [
                'id' => 'RESOLUTION_NUMBER',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Resolution Number',
            ],
        ]);
        
        $options->add([
            'type' => MultiCheckbox::class,
            'name' => 'OPTIONS',
            'options' => [
                'label' => 'Options',
                'value_options' => [
                    '0' => 'Soul Source Purchase',
                    '1' => 'Emergency Purchase',
                    '2' => 'Bid Waiver',
                    '3' => 'Cooperative Contract',
                ],
            ],
        ]);
        
        $this->add($options);
        
        $this->add([
            'name' => 'VENDOR',
            'type' => SignatureFieldset::class,
            'attributes' => [
                'id' => 'VENDOR',
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Vendor Information',
            ]
        ]);
    }
}