<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Laminas\InputFilter\FileInput;
use Frontend\Contract\InputFilter\Input\SelectInput;
use Frontend\Contract\InputFilter\Input\TextInput;

class UploadFileInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        parent::init();
        
        return $this
            ->add(new TextInput('contract-number', false))
            ->add(new TextInput('parent', false))
            ->add(new FileInput('FILE', true))
            ->add(new SelectInput('DOCTYPE', true))
            ->add(new SelectInput('DEPARTMENT', false))
            ->add(new TextInput('PROJECT_NAME', false))
        ;
    }
}