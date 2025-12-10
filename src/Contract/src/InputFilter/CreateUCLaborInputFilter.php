<?php
declare(strict_types = 1);
namespace Frontend\Contract\InputFilter;

use Frontend\Contract\InputFilter\Input\CheckboxInput;
use Frontend\Contract\InputFilter\Input\TextInput;

class CreateUCLaborInputFilter extends CreateContractInputFilter
{

    public function init(): self
    {
        parent::init();

        return $this->add(new TextInput('RESOLUTION_NUMBER'))
            ->add(new CheckboxInput('COOP_CONTRACT', false))
            ->add(new CheckboxInput('BID_WAIVER', false))
            ->add(new CheckboxInput('EMER_PURCHASE', false))
            ->add(new CheckboxInput('SOLE_SOURCE_PURCHASE', false));
    }
}