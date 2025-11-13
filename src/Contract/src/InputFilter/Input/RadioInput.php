<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter\Input;

use Laminas\InputFilter\Input;

class RadioInput extends Input
{
    public function __construct(?string $name = null, bool $required = true)
    {
        parent::__construct($name);
        
        $this->setRequired($required);
    }
}