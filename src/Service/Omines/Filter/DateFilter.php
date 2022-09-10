<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Service\Omines\Filter;

use DateTime;
use Omines\DataTablesBundle\Filter\AbstractFilter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateFilter extends AbstractFilter
{
    /** @var string */
    protected $placeholder;



    public function __construct(array $options = [])
    {
        $this->set($options);
    }

    /**
     * @return $this
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'template_html' => '@DataTables/Filter/date.html.twig',
                'template_js' => '@DataTables/Filter/date.js.twig',
                'placeholder' => null,
            ])
            ->setAllowedTypes('placeholder', ['null', 'string']);

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    

    /**
     * {@inheritdoc}
     */
    public function isValidValue($value): bool
    {
        return DateTime::createFromFormat('Y-m-d', $value) ? true : false;
    }

    public function set(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        foreach ($resolver->resolve($options) as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
