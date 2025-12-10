<?php

declare(strict_types=1);

namespace Frontend\Workflow\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;
use Frontend\Workflow\InputFilter\Input\ConfirmDeleteWorkflowInput;

/**
 * @phpstan-type DeleteWorkflowDataType array{}
 * @extends AbstractInputFilter<DeleteWorkflowDataType>
 */
class DeleteWorkflowInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new ConfirmDeleteWorkflowInput('confirmation'))
            ->add(new CsrfInput('deleteWorkflowCsrf', true));
    }
}
