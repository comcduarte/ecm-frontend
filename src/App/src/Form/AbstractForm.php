<?php
declare(strict_types=1);

namespace Frontend\App\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterInterface;

abstract class AbstractForm extends Form
{
    protected InputFilterInterface $inputFilter;
    
    public function getInputFilter(): InputFilterInterface
    {
        return $this->inputFilter;
    }
}