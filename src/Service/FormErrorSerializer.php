<?php

namespace App\Service;
use Symfony\Component\Form\FormInterface;

class FormErrorSerializer
{
    /**
     * @param FormInterface $form
     * @param bool $flat_array
     * @param bool $add_form_name
     * @param string $glue_keys
     * @return mixed
     */
    public function serializeFormErrors(FormInterface $form, bool $flat_array = false, bool $add_form_name = false, string $glue_keys = '_'): array
    {
        $errors           = [];
        $errors['global'] = [];
        $errors['fields'] = [];

        foreach ($form->getErrors(true, true) as $error) {
            $errors['global'][] = $error->getMessage();
        }

        $errors['fields'] = $this->serialize($form);

        if ($flat_array) {

            $errors['fields'] = $this->arrayFlatten(
                $errors['fields'],
                $glue_keys,
                (($add_form_name) ? $form->getName() : '')
            );
        }
        return $errors;
    }



    /**
     * @param FormInterface $form
     * @return mixed
     */
    private function serialize(FormInterface $form): array
    {
        $local_errors = [];
        foreach ($form->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $local_errors[$key] = $error->getMessage();
            }

            if (count((array) $child->getIterator()) > 0) {
                $local_errors[$key] = $this->serialize($child);
            }

        }

        return $local_errors;

    }



    /**
     * @param array $array
     * @param string $separator
     * @param string $flattened_key
     * @return mixed
     */
    private function arrayFlatten(array $array, string $separator = "_", string $flattened_key = ''): array
    {
        $flattenedArray = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = array_merge(
                    $flattenedArray,
                    $this->arrayFlatten(
                        $value,
                        $separator,
                        (strlen($flattened_key) > 0 ? $flattened_key . $separator : "") . $key
                    )
                );
            } else {
                $flattenedArray[(strlen($flattened_key) > 0 ? $flattened_key . $separator : "") . $key] = $value;
            }
        }
        return $flattenedArray;
    }
}

