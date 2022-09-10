<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Parcours;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('libelle')
            ->add('description')
            ->add('observations')
            ->add('parcours',EntityType::class, [
        'required' => false,
        'class' => Parcours::class,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->orderBy('u.id', 'DESC');
        },
        'label' => 'Selectionnez un parcours',
        'placeholder' => 'Selectionnez un parcours',
        'choice_label' => function ($user) {
            return $user->getCode() . ' ' . $user->getLibelle();
        },
                'attr' => ['class' => 'form-control has-select2', 'id' => 'validationCustom05']

    ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
            //'doc_required' => true,
        ]);

    }
}
