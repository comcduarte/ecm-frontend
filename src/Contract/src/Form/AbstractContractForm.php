<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Form;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Session\Container;
use Laminas\Validator\NotEmpty;

abstract class AbstractContractForm extends Form implements InputFilterProviderInterface
{
    
    public function __construct(?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
    }
    
    public function init(): void
    {
        $this->add(
            (new Csrf('createContractCsrf'))
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
            'createContractCsrf' => [
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