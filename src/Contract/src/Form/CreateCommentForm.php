<?php
declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\InputFilter\CreateCommentInputFilter;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Session\Container;

class CreateCommentForm extends AbstractForm
{
    public function __construct(?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);
        
        $this->init();
        
        $this->setAttribute('id', 'create-comment-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');
        
        $this->inputFilter = new CreateCommentInputFilter();
        $this->inputFilter->init();
    }
    
    public function init(): void
    {
        $this->add([
            'name' => 'MESSAGE',
            'type' => Text::class,
            'attributes' => [
                'id' => 'MESSAGE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Message',
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