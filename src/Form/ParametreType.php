<?php

namespace App\Form;

use App\Entity\Parametre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       //
        $builder
            ->add('titre')
            ->add('logo', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('couleurHeader')
            ->add('couleurSide')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Parametre::class,
        ]);
    }
}
