<?php

declare(strict_types=1);

namespace Frontend\Workflow\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type CreateWorkflowDataType array{}
 * @extends AbstractInputFilter<CreateWorkflowDataType>
 */
class CreateWorkflowInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new CsrfInput('createWorkflowCsrf', true));
    }
}
