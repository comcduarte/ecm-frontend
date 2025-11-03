<?php
declare(strict_types = 1);
namespace Frontend\Contract\InputFilter\Input;

use Core\App\Message;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\InputFilter\Input;
use Laminas\Validator\NotEmpty;


class TextInput extends Input
{
    public function __construct(?string $name = null, bool $required = true)
    {
        parent::__construct($name);
        
        $this->setRequired($required);
        
        $this->getFilterChain()
            ->attachByName(StringTrim::class)
            ->attachByName(StripTags::class);
        
            $this->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => Message::VALIDATOR_REQUIRED_FIELD,
            ], true);
    }
}
