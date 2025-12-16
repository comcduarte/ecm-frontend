<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\Contract\InputFilter\Input\FileInput;
use Frontend\Contract\InputFilter\Input\TextInput;

class CreateAmendmentInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        parent::init();
        
        return $this
        ->add(new TextInput('parent', false))
        ->add(new FileInput('FILE', true))
        ->add(new TextInput('PROJECT_NAME', false))
        ->add(new TextInput('CONTRACT_ID', false))
        ;
    }
}