<?php
declare(strict_types=1);

namespace Frontend\App\Form;

use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\Factory as InputFilterFactory;

abstract class AbstractForm extends Form
{
    protected ?InputFilterInterface $inputFilter = null;
    
    public function getInputFilter(): InputFilterInterface
    {
        if ($this->inputFilter instanceof InputFilterInterface) {
            
            return $this->inputFilter;
            
        }
        
        $spec = $this->getInputFilterSpecification();
        $factory = new InputFilterFactory();
        $built = $factory->createInputFilter($spec);
        
        if (! $built instanceof InputFilterInterface) {
            $built = new InputFilter();
        }
        
        
        $this->inputFilter = $built;
        
        return $this->inputFilter;
    }
    
//     abstract public function getInputFilterSpecification(): array;
}