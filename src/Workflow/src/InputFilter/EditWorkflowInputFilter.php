<?php

declare(strict_types=1);

namespace Frontend\Workflow\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type EditWorkflowDataType array{}
 * @extends AbstractInputFilter<EditWorkflowDataType>
 */
class EditWorkflowInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new CsrfInput('editWorkflowCsrf', true));
    }
}
