<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Laminas\InputFilter\FileInput;

class UploadFileInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        parent::init();
        
        return $this
            ->add(new FileInput('FILE', true));
    }
}