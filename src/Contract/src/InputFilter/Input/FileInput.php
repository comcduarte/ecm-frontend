<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter\Input;

use Laminas\Validator\File\Extension;
use Laminas\Validator\File\Size;

class FileInput extends FileInput
{
    public function __construct(?string $name = null, bool $required = true)
    {
        parent::__construct($name);
        
        $this->setRequired($required);
        
        $this->getValidatorChain()
            ->attachByName(Extension::class, [
                'extension' => ['json','pdf'],
            ])
            ->attachByName(Size::class, [
                'max' => '2MB',
            ])
        ;
    }
}