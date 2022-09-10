<?php

namespace App\Form;

use App\Entity\Icons;
use App\Service\Services;
use App\Entity\Groupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeType extends AbstractType
{
    public $listeLien;
    private $route;

    public function __construct(Services $listeLien,RequestStack $route,Container $container)
    {
        $this->listeLien=$listeLien;
    }

    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('titre',TextType::class,["label" => false,])
            ->add('lien',ChoiceType::class,
                [
                    'expanded'     => false,
                    'placeholder' => 'Choisir un lien',
                    'required'     => true,
                    'label'=>false,
                    /*   'attr' => ['class' => 'select2_multiple'],
                       'multiple' => true,*/
                    //'choices_as_values' => true,

                    'choices'  => array_flip($this->listeLien->listeLien()),

                ])
            ->add('icon',EntityType::class,[
                'class' => Icons::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.active = :val')
                        ->setParameter('val', 1)
                        ->orderBy('u.id', 'DESC');
                },
                'label'=>false,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
