<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter\Input;

use Laminas\InputFilter\FileInput as LaminasFileInput;
use Laminas\Validator\File\Extension;
use Laminas\Validator\File\Size;

class FileInput extends LaminasFileInput
{
    public function __construct(?string $name = null, bool $required = true)
    {
        parent::__construct($name);
        
        $this->setRequired($required);
        
        $this->getValidatorChain()
            ->attachByName(Extension::class, [
                'extension' => ['docx','pdf','doc'],
            ])
            ->attachByName(Size::class, [
                'max' => '50MB',
                //-- This is a BOX UPLOAD Limit --//
            ])
        ;
    }
}