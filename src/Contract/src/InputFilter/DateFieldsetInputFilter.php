<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Frontend\Contract\InputFilter\Input\DateInput;
use Laminas\InputFilter\InputFilter;

class DateFieldsetInputFilter extends InputFilter
{
    public function init(): self
    {
        return $this
            ->add(new DateInput('EXEC_DATE', true))
            ->add(new DateInput('START_DATE', true))
            ->add(new DateInput('CONTRACT_END_DATE', true));
    }
}