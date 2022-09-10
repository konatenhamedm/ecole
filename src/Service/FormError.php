<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class FormError extends FormErrorBuilder
{
    /**
     * @param FormErrorSerializer $formErrorSerializer
     */

    public function __construct(FormErrorSerializer $formErrorSerializer)
    {
        $this->formErrorSerializer = $formErrorSerializer;
    }



    /**
     * @param FormInterface $form
     * @param bool $unique
     * @return mixed
     */
    public function all(FormInterface $form, bool $unique = false): array
    {
        $errors = $this->flatten(array_values($this->formErrorSerializer->serializeFormErrors($form, true)));
        return $unique ? array_values(array_unique($errors)) : $errors;
    }
}

