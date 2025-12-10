<?php

declare(strict_types=1);

namespace Frontend\Template\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;

/**
 * @phpstan-type CreateTemplateDataType array{}
 * @extends AbstractInputFilter<CreateTemplateDataType>
 */
class CreateTemplateInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below CDD CsrfInput doesn't exist, along with AbstractForm

        return $this
            ->add(new CsrfInput('createTemplateCsrf', true));
    }
}
