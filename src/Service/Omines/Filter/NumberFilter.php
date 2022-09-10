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

use Omines\DataTablesBundle\Filter\AbstractFilter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberFilter extends AbstractFilter
{
    /** @var string */
    protected $placeholder;

    protected $operators = [];

    protected $defaultOperator;

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
                'template_html' => '@DataTables/Filter/number.html.twig',
                'template_js' => '@DataTables/Filter/number.js.twig',
                'placeholder' => null,
                'operators' => [],
                'default_operator' => '='
            ])
            ->setAllowedTypes('placeholder', ['null', 'string'])
            ->setAllowedTypes('operators', 'array')
            ->setAllowedTypes('default_operator', ['string']);

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
     * @return mixed
     */
    public function getOperators()
    {
        return $this->operators;
    }


     /**
     * @return mixed
     */
    public function getDefaultOperator()
    {
        return $this->defaultOperator;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidValue($value): bool
    {
        return true;
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
