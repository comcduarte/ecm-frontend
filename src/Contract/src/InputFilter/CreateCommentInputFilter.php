<?php
declare(strict_types=1);

namespace Frontend\Contract\InputFilter;

use Core\App\InputFilter\AbstractInputFilter;
use Frontend\Contract\InputFilter\Input\TextInput;

class CreateCommentInputFilter extends AbstractInputFilter
{
    public function init(): self
    {
        return $this
            ->add(new TextInput('MESSAGE', true));
    }
}