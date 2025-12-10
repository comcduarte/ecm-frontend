<?php

declare(strict_types=1);

namespace Frontend\Template\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;
use Frontend\Template\InputFilter\Input\ConfirmDeleteTemplateInput;

/**
 * @phpstan-type DeleteTemplateDataType array{}
 * @extends AbstractInputFilter<DeleteTemplateDataType>
 */
class DeleteTemplateInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new ConfirmDeleteTemplateInput('confirmation'))
            ->add(new CsrfInput('deleteTemplateCsrf', true));
    }
}
