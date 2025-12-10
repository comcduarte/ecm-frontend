<?php

declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type CreateCCDataType array{}
 * @extends AbstractInputFilter<CreateCCDataType>
 */
class CreateCCInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new CsrfInput('createCCCsrf', true));
    }
}
