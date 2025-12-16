<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\Contract\InputFilter\CreateAmendmentInputFilter;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;

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
            
            $this->inputFilter = new CreateAmendmentInputFilter();
            $this->inputFilter->init();
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
}