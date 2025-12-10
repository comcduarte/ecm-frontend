<?php

declare(strict_types=1);

namespace Frontend\Contract\Form;

use Frontend\App\Form\AbstractForm;
use Frontend\Contract\Form\Fieldset\DateFieldset;
use Frontend\Contract\Form\Fieldset\SignatureFieldset;
use Frontend\Contract\InputFilter\CreateUCLTSInputFilter;
use Laminas\Form\Fieldset;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;

/**
 * @phpstan-import-type CreateUCLTSDataType from CreateUCLTSInputFilter
 * @extends AbstractForm<CreateUCLTSDataType>
 */
class CreateUCLTSForm extends AbstractForm
{
    /**
     * @throws ExceptionInterface
     */
    public function __construct(
        ?string $name = null,
        array $options = [],
    ) {
        parent::__construct($name, $options);

        $this->init();

        $this->setAttribute('id', 'u-clts-form');
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', 'novalidate');

        $this->inputFilter = new CreateUCLTSInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
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

        $this->add([
            'name' => 'TYPE',
            'type' => Hidden::class,
            'attributes' => [
                'id' => 'TYPE',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Document Template',
            ],
        ]);
        
        $this->add(
            (new Csrf('createUCLTSCsrf'))
                ->setOptions([
                    'csrf_options' => ['timeout' => 3600, 'session' => new Container()],
                ])
                ->setAttribute('required', true)
        );
        $this->add(
            (new Submit('submit'))
                ->setAttribute('type', 'submit')
                ->setAttribute('value', 'Save')
                ->setAttribute('class', 'btn btn-primary btn-color btn-sm')
        );
    }
}
