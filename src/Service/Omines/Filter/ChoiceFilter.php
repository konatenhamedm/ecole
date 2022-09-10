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

class ChoiceFilter extends AbstractFilter
{
    /** @var string */
    protected $placeholder;

    /** @var array */
    protected $choices = [];


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
                'template_html' => '@DataTables/Filter/select.html.twig',
                'template_js' => '@DataTables/Filter/select.js.twig',
                'placeholder' => null,
                'data-url' => null,
                'choices' => [],
            ])
            ->setAllowedTypes('placeholder', ['null', 'string'])
            ->setAllowedTypes('choices', ['array']);

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
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidValue($value): bool
    {
        return array_key_exists($value, $this->choices);
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
