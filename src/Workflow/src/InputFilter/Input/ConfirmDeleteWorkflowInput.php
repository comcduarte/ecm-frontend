<?php

declare(strict_types=1);

namespace Frontend\Workflow\InputFilter\Input;

use Laminas\InputFilter\Input;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;

class ConfirmDeleteWorkflowInput extends Input
{
    public function __construct(
        ?string $name = null,
        bool $isRequired = true,
    ) {
        parent::__construct($name);

        $this->setRequired($isRequired);

        $this->getValidatorChain()
            ->attachByName(NotEmpty::class, [
                'message' => 'Please confirm the Workflow deletion.',
            ], true)
            ->attachByName(InArray::class, [
                'message'  => 'Please confirm the Workflow deletion.',
                'haystack' => [
                    'yes',
                ],
            ], true);
    }
}
