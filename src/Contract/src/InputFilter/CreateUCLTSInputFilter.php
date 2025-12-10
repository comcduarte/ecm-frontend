<?php

declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type CreateUCLTSDataType array{}
 * @extends AbstractInputFilter<CreateUCLTSDataType>
 */
class CreateUCLTSInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new CsrfInput('createUCLTSCsrf', true));
    }
}
