<?php

namespace App\Form;

use App\Entity\{Icons, Module, ModuleParent};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('icon',EntityType::class,[
                'class' => Icons::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.active = :val')
                        ->setParameter('val', 1)
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'code',

            ])
            ->add('ordre',ChoiceType::class,
                [
                    'expanded'     => false,
                    'placeholder' => 'Choisir un ordre',
                    'required'     => true,
                    'label'=>false,
                    /*   'attr' => ['class' => 'select2_multiple'],
                       'multiple' => true,*/
                    //'choices_as_values' => true,

                    'choices'  =>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20],

                ])
            ->add('parent', EntityType::class, [
                'class' => ModuleParent::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.active = :val')
                        ->setParameter('val', 1)
                        ->orderBy('u.id', 'DESC');
                },
                'choice_label' => 'titre',

            ])
            ->add('groupes', CollectionType::class, [
                'entry_type' => GroupeType::class,
                'entry_options' => [
                    'label' => false
                ],
                'label' => false,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'attr' => [
                    'class' => 'collection',
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}
