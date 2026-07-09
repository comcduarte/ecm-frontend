<?php

declare(strict_types=1);

namespace Frontend\App\Form\View\Helper;

use Laminas\Form\ElementInterface;
use Laminas\Form\View\Helper\FormCheckbox;
use Laminas\Form\View\Helper\FormRow as LaminasFormRow;

class FormRow extends LaminasFormRow
{
    public function render(ElementInterface $element, ?string $labelPosition = null): string
    {
        if ($element::class === \Laminas\Form\Element\Checkbox::class) {

            // Give Bootstrap classes if they aren't already present.
            $class = trim(($element->getAttribute('class') ?? '') . ' form-check-input');
            $element->setAttribute('class', $class);

            $labelAttributes = $element->getLabelAttributes();
            $labelAttributes['class'] =
                trim(($labelAttributes['class'] ?? '') . ' form-check-label');
            $element->setLabelAttributes($labelAttributes);

            /** @var FormCheckbox $checkbox */
            $checkbox = $this->getView()->plugin('formCheckbox');

            $label = $this->getView()->plugin('formLabel');
            $errors = $this->getView()->plugin('formElementErrors');

            return sprintf(
                '<div class="form-check">%s%s%s</div>',
                $checkbox($element),
                $label($element),
                $errors($element)
            );
        }

        return parent::render($element, $labelPosition);
    }
}