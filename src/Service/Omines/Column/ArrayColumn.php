<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Service\Omines\Column;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Omines\DataTablesBundle\Column\AbstractColumn;

class ArrayColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function normalize($value): string
    {
        $states = $this->options['states'];
        $flats = array_values($states);
        $data = array_keys($value);
        $tmp = [];
       
        foreach ($data as $val) {
            foreach ($flats as $key => $values) {
                
                if (in_array($val, array_keys($values))) {
                    $tmp[] = $val == 'cree' ? 'Crée': $values[$val]['label'];
                }
                
            }
            
        }
       
        return implode($this->getSeparator(), $tmp);
    }


    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('states');
        $resolver
            ->setDefault('raw', false)
            ->setDefault('separator', '<br />')
            ->setAllowedTypes('raw', 'bool')
            ->setAllowedTypes('separator', 'string')
            ->setAllowedTypes('states', 'array');
           

        return $this;
    }


    public function getSeparator(): string
    {
        return $this->options['separator'];
    }

    public function isRaw(): bool
    {
        return $this->options['raw'];
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isValidForSearch($value)
    {
        return true;
    }
}
