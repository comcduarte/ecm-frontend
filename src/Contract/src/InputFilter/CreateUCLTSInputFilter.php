<?php

declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\App\InputFilter\Input\CsrfInput;
use Frontend\Contract\Form\Fieldset\SignatureFieldset;
use Frontend\Contract\InputFilter\Input\CheckboxInput;
use Frontend\Contract\InputFilter\Input\SelectInput;
use Frontend\Contract\InputFilter\Input\TextInput;
use Laminas\InputFilter\InputFilter;

/**
 * @phpstan-type CreateUCLTSDataType array{}
 * @extends AbstractInputFilter<CreateUCLTSDataType>
 */
class CreateUCLTSInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        /**
         * 
         * @var \Frontend\Contract\InputFilter\DateFieldsetInputFilter $datesFilter
         */
        $datesFilter = (new DateFieldsetInputFilter())->init();
        
        /**
         * 
         * @var \Frontend\Contract\Form\Fieldset\SignatureFieldset $signatureFieldset
         */
        $signatureFieldset = new SignatureFieldset();
        $signatureInputFilter = new InputFilter();
        
        foreach ($signatureFieldset->getInputFilterSpecification() as $name => $spec) {
            $signatureInputFilter->add($spec, $name);
        }
        
        /**
         * 
         * @var \Laminas\InputFilter\InputFilter $infoInputFilter
         */
        $infoInputFilter = new InputFilter();
        $infoInputFilter
            ->add(new TextInput('PROJECT_NAME', true))
            ->add(new TextInput('RESOLUTION_NUMBER', false))
            ->add(new CheckboxInput('OPTIONS', true))
            ->add(new TextInput('CONTRACT_AMOUNT', false));
        
        return $this 
            ->add($datesFilter, 'DATES')
            ->add($signatureInputFilter, 'VENDOR')
            ->add($infoInputFilter, 'INFO')
        
            ->add(new TextInput('DEPARTMENT', false))
            
            ->add(new TextInput('ENTITY', false))
            ->add(new TextInput('parent', false))
            ->add(new TextInput('DOCTYPE', false))
            
            ->add(new SelectInput('TYPE', false))
            ->add(new CsrfInput('createUCLTSCsrf', true));
    }
}
