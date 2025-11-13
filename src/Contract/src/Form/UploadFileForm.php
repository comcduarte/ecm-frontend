<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\UploadFileInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Submit;
use Laminas\Session\Container;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Hidden;

class UploadFileForm extends AbstractForm
{
    public function __construct(?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
        
        $this->init();
        
        $this->setAttribute('id', 'upload-file-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        
        $this->inputFilter = new UploadFileInputFilter();
        $this->inputFilter->init();
    }
    
    public function init(): void
    {
        $this->add([
            'name' => 'contract-number',
            'type' => Hidden::class,
        ]);
        
        $this->add([
            'name' => 'DOCTYPE',
            'type' => Select::class,
            'attributes' => [
                'id' => 'DOCTYPE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Document Type',
                'value_options' => [
                    'Certificate of Insurance' => 'Certificate of Insurance',
                    'Department Head Certification Page' => 'Department Head Certification Page',
                    'Certificate of Surety' => 'Certificate of Surety',
                ],
            ],
        ]);
        
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
        
        $this->add(
            (new Csrf('createContractCsrf'))
            ->setOptions([
                'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
            ])
            ->setAttribute('required', true)
            );
        
        $this->add([
            'name' => 'SUBMIT',
            'type' => Submit::class,
            'attributes' => [
                'value' => 'Submit',
                'class' => 'btn btn-primary form-control mt-4',
                'id' => 'SUBMIT',
            ],
        ],['priority' => 0]);
    }
}