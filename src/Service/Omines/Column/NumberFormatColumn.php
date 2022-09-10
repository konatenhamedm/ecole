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

class NumberFormatColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function normalize($value): string
    {
        $decimalLength = $this->getDecimalLength();
        if (strpos(strval($value), '.')) {
            [,$decimal] = explode('.', strval($value));
            $decimalLength = strlen($decimal);
        }

        $val = trim(number_format(floatval($value), $decimalLength, ',', ' '));
        $val = preg_replace('/,00$/', '', $val);
        return $val.' '.$this->options['unite'];
    }


    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('decimal');
        $resolver->setRequired('unite');

        $resolver
            ->setDefault('raw', false)
            ->setDefault('decimal', 0)
            ->setDefault('unite', '')
            ->setDefault('className', 'text-right')
            ->setAllowedTypes('raw', 'bool')
            ->setAllowedTypes('decimal', 'int')
            ->setAllowedTypes('unite', 'string')
        ;

        return $this;
    }


    public function getDecimalLength(): int
    {
        return intval($this->options['decimal']);
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
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }
}