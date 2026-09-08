<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Session\Container;
use Laminas\Validator\NotEmpty;

class SearchContractForm extends AbstractForm
{
    public function __construct(?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
        
        $this->init();
        
        $this->setAttribute('id', 'search-contract-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');
        $this->setAttribute('method', 'GET');
    }
    
    public function init(): void
    {
        parent::init();
        
        $this->add([
            'name' => 'query',
            'type' => Text::class,
            'attributes' => [
                'id' => 'query',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Search for',
            ],
        ]);
        
        $this->add(
            (new Csrf('searchContractCsrf'))
            ->setOptions([
                'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
            ],['priority' => -100])
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
        ],['priority' => -100]);
    }
    
    public function getInputFilterSpecification()
    {
        return [
            'PROJECT_NAME' => [
                'required' => true,
                'filters' => [
                    ['name' => StringTrim::class],
                    ['name' => StripTags::class],
                ],
                'validators' => [
                    ['name' => NotEmpty::class],
                ],
            ],
        ];
    }
}