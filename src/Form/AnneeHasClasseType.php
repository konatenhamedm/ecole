<?php

namespace App\Form;

use App\Entity\Annee;
use App\Entity\AnneeHasClasse;
use App\Entity\Classe;
use App\Entity\Parcours;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnneeHasClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scolarite',MoneyType::class,[
        //'label' => false,
             ])
            ->add('description')
            ->add('observations')
            ->add('annee',EntityType::class, [
                'required' => false,
                'class' => Annee::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'label' => 'Selectionnez une annee',
                'placeholder' => 'Selectionnez une annee',
                'choice_label' => function ($user) {
                    return $user->getCode() . ' ' . $user->getLibelle();
                },
                'attr' => ['class' => 'form-control select2', 'id' => 'validationCustom05']

            ])
            ->add('classe',EntityType::class, [
                'required' => false,
                'class' => Classe::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'label' => 'Selectionnez une classe',
                'placeholder' => 'Selectionnez une classe',
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
            'data_class' => AnneeHasClasse::class,
        ]);
    }
}
