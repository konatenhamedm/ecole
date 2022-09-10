<?php

namespace App\Form;

use App\Entity\AnneeHasClasse;
use App\Entity\Eleve;
use App\Entity\Parcours;
use App\Entity\Scolarite;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScolariteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('scolaritePersonne',MoneyType::class,[
                'label' => "Scolarite personne (non affecté de l'etat )",
            ])
           // ->add('description')
            //->add('observations')

            ->add('eleve',EntityType::class, [
                'required' => false,
                'class' => Eleve::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'label' => 'Selectionnez un eleve',
                'placeholder' => 'Selectionnez un eleve',
                'choice_label' => function ($user) {
                    return $user->getMatricule() . ' - ' . $user->getNom().' - ' . $user->getPrenoms();
                },
                'attr' => ['class' => 'form-control has-select2', 'id' => 'validationCustom05']

            ])
            ->add('ahc',EntityType::class, [
                'required' => false,
                'class' => AnneeHasClasse::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'DESC');
                },
                'label' => "Scolarite personne (affecté de l'etat )",
                'placeholder' => 'Selectionnez une annee has classe',
                'choice_label' => function ($user) {
                    return $user->getClasse()->getParcours()->getLibelle().' - '.$user->getAnnee()->getLibelle() .' - ' .$user->getScolarite();
                },
                'attr' => ['class' => 'form-control has-select2', 'id' => 'validationCustom05']

            ])
            ->add('versements', CollectionType::class, [
                'entry_type' => VersementType::class,
                'entry_options' => [
                    'label' => false
                ],
                'allow_add' => true,
                'label' => false,
                'by_reference' => false,
                'allow_delete' => true,
                'prototype_name' => '__workflow__',
                'prototype' => true,

            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $submittedData = $event->getData();

            if (!array_key_exists('workflows', $submittedData)) {
                return;
            }

            //Re-index the array to ensure the forms stay in the submitted order.
            $submittedData['workflows'] = array_values($submittedData['workflows']);

            if (array_key_exists('documentTypeActes', $submittedData)) {
                $submittedData['documentTypeActes'] = array_values($submittedData['documentTypeActes']);
            }


            $event->setData($submittedData);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scolarite::class,
        ]);
    }
}
