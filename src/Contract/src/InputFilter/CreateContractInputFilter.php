<?php

declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;
use Frontend\Contract\InputFilter\Input\CheckboxInput;
use Frontend\Contract\InputFilter\Input\DateInput;
use Frontend\Contract\InputFilter\Input\TextInput;
use Frontend\Contract\InputFilter\Input\SelectInput;

/**
 * @phpstan-type CreateContractDataType array{}
 * @extends AbstractInputFilter<CreateContractDataType>
 */
class CreateContractInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        // chain inputs below

        return $this
            ->add(new TextInput('ENTITY_NAME', false))
            ->add(new TextInput('ENTITY_LOGO', false))
            ->add(new TextInput('ENTITY_ALIAS', false))
        
            ->add(new DateInput('START_DATE', false))
            ->add(new DateInput('END_DATE', false))
            
            ->add(new CheckboxInput('EXCEEDS_100K', false))
            ->add(new TextInput('project-name', false))
            ->add(new TextInput('RESOLUTION_NUMBER', false))
            
            ->add(new TextInput('VENDOR_NAME', false))
            ->add(new TextInput('VENDOR_SIGNER', false))
            ->add(new TextInput('VENDOR_SIGNER_EMAIL', false))
            
            ->add(new TextInput('DEPARTMENT', false))
            ->add(new TextInput('parent', false))
            
            ->add(new SelectInput('TYPE', false))
            ->add(new CsrfInput('createContractCsrf', false));
    }
}
