<?php

declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type CreatePostUCLaborFormDataType array{}
 * @extends AbstractInputFilter<CreatePostUCLaborFormDataType>
 */
class CreatePostUCLaborFormInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new CsrfInput('createPostUCLaborFormCsrf', true));
    }
}
