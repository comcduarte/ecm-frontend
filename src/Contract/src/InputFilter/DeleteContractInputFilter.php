<?php

declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;
use Frontend\Contract\InputFilter\Input\ConfirmDeleteContractInput;

/**
 * @phpstan-type DeleteContractDataType array{}
 * @extends AbstractInputFilter<DeleteContractDataType>
 */
class DeleteContractInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new ConfirmDeleteContractInput('confirmation'))
            ->add(new CsrfInput('deleteContractCsrf', true));
    }
}
