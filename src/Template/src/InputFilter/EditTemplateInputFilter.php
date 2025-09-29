<?php

declare(strict_types=1);

namespace Frontend\Template\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type EditTemplateDataType array{}
 * @extends AbstractInputFilter<EditTemplateDataType>
 */
class EditTemplateInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new CsrfInput('editTemplateCsrf', true));
    }
}
