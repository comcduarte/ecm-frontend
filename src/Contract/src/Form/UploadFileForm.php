<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\UploadFileInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Session\Container;

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
                    
                    'Amendment Supplied by Vendor' => 'Amendment Supplied by Vendor',
                    'City Lease' => 'City Lease',
                    'Contract Amendment' => 'Contract Amendment',
                    'Contract Supplied by Vendor' => 'Contract Supplied by Vendor',
                    'Uniform Amendment' => 'Uniform Amendment',
                    'Uniform Artist Contract' => 'Uniform Artist Contract',
                    'Uniform Contract for Goods and Services' => 'Uniform Contract for Goods and Services',
                    'Uniform Library Contract' => 'Uniform Library Contract',
                    
                    'Additional Indemnification Agreement' => 'Additional Indemnification Agreement',
                    'Appendix - Insurance Requirements' => 'Appendix - Insurance Requirements',
                    'Appendix A - Statement of Work' => 'Appendix A - Statement of Work',
                    'Appendix D - Memo' => 'Appendix D - Memo',
                    'Bid Documents' => 'Bid Documents',
                    'Bid Results' => 'Bid Results',
                    'Building Committee Meeting Minutes' => 'Building Committee Meeting Minutes',
                    'Certificate of Insurance' => 'Certificate of Insurance',
                    'Certificate of Insurance - Bus Auto Liability' => 'Certificate of Insurance - Bus Auto Liability',
                    'Certificate of Insurance - General Liability' => 'Certificate of Insurance - General Liability',
                    'Certificate of Insurance - Miscellaneous' => 'Certificate of Insurance - Miscellaneous',
                    'Certificate of Insurance - Professional Liability' => 'Certificate of Insurance - Professional Liability',
                    'Change Order' => 'Change Order',
                    'City - Standard Insurance Requirements' => 'City - Standard Insurance Requirements',
                    'Contract Award' => 'Contract Award',
                    'Cooperative Contract Agreement' => 'Cooperative Contract Agreement',
                    'LIBRARY - Standard Insurance Requirements' => 'LIBRARY - Standard Insurance Requirements',
                    'Mayor Contract Signature Page' => 'Mayor Contract Signature Page',
                    'Misc. Supporting Documentation' => 'Misc. Supporting Documentation',
                    'POLLUTION CITY - Standard Insurance Requirements' => 'POLLUTION CITY - Standard Insurance Requirements',
                    'PROFESSIONAL CITY - Standard Insurance Requirements' => 'PROFESSIONAL CITY - Standard Insurance Requirements',
                    'PROFESSIONAL LIBRARY - Standard Insurance Requirements' => 'PROFESSIONAL LIBRARY - Standard Insurance Requirements',
                    'Professional Service Memorandum of Agreement' => 'Professional Service Memorandum of Agreement',
                    'Purchase Order' => 'Purchase Order',
                    'Purchasing RFP' => 'Purchasing RFP',
                    'RFP / RFQ' => 'RFP / RFQ',
                    
                    'Formal Opinion' => 'Formal Opinion',
                    
                    
                    
                    
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
        
        /**
         * DEPARTMENT INFORMATION
         */
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