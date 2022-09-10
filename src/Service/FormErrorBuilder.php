<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

abstract class FormErrorBuilder
{

    /**
     * @param $form
     */
    abstract public function all(FormInterface $form, bool $unique = false);

    /**
     * @param $array
     * @return mixed
     */
    protected function flatten(array $array): array
    {
        $return = [];
        array_walk_recursive($array, function ($x) use (&$return) {
            $return[] = $x;

        });
        return $return;
    }

}

