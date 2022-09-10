<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class)
            ->add('prenoms')
            ->add('email',EmailType::class,[
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre adresse email'
                ]

                ]
               )
            ->add('password',RepeatedType::class,[
                'type'=> PasswordType::class,
                'required'=> $options['required'],
                'invalid_message'=>'Le mot de passe et la confirmation doivent être identique',
             'mapped'=>true,

                'first_options'=> ['label'=>'Mot de passe'],
                'second_options'=> ['label'=>'Confirmer votre mot de passe']
            ])

            ->add('roles' ,ChoiceType::class,
                [
                    'expanded'     => false,
                    'placeholder' => 'Choisir un role',
                    'required'     => true,
                   // 'attr' => ['class' => '],
                    'multiple' => true,
                    //'choices_as_values' => true,

                    'choices'  => array_flip([
                        'ROLE_USER'        => 'Utilisateur',
                        'ROLE_ADMIN'       => 'Administrateur',
                        'ROLE_SUPER_ADMIN' => 'Super Administrateur',
                    ]),
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
