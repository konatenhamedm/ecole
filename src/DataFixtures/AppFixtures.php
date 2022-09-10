<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use App\Entity\Icons;
use App\Entity\Module;
use App\Entity\ModuleParent;
use App\Entity\User;
use App\Repository\ModuleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $module;
    private $encode;

    public function __construct(ModuleRepository $repository, UserPasswordHasherInterface $encode)
    {
        $this->module = $repository->find(17);
        $this->encode = $encode;

    }

    public function load(ObjectManager $manager): void
    {

        $array = [
            "1"=>"fe-home",
            "2"=> "fe-slack",
             "3"=>"fe-layers",
             "4"=>"fe-shopping-bag",
             "5"=>"fe-users",
             "6"=>"fe-chevron-right",
             "7"=>"fe-grid",
             "8"=>"fe-send",
             "9"=>"fe-map-pin",
             "10"=>"fe-bar-chart-2",
             "11"=>"fe-setting",
             "12"=>"fe-mail",
             "13"=>"fe-book-open",
        ];

        foreach ($array as $e){
        $icon = new Icons();
        $icon->setCode($e);
        $icon->setImage("");
        $icon->setActive(1);
        $manager->persist($icon);

    }


        $icon1 = new Icons();
        $icon1->setCode("tio-apps");
        $icon1->setImage("");
        $icon1->setActive(1);
        $manager->persist($icon1);

        $parent = new ModuleParent();
        $parent->setTitre('PARAMETRAGES');
        $parent->setOrdre(1);
        $parent->setActive(1);
        $manager->persist($parent);

        $mod2 = new Module();
        $mod2->setTitre('Paramétrage')
            ->setOrdre(1)
            ->setActive(1)
            ->setIcon($icon)
            ->setParent($parent);
        $manager->persist($mod2);

        $groupe6 = new Groupe();
        $groupe6->setIcon($icon1)
            ->setLien('typeClient')
            ->setModule($mod2)
            ->setOrdre(1)
            ->setTitre('Type de client');
        $manager->persist($groupe6);

        $groupe7 = new Groupe();
        $groupe7->setIcon($icon1)
            ->setLien('typeActe')
            ->setModule($mod2)
            ->setOrdre(2)
            ->setTitre("Type d'acte");
        $manager->persist($groupe7);

        $groupe8 = new Groupe();
        $groupe8->setIcon($icon1)
            ->setLien('frais')
            ->setModule($mod2)
            ->setOrdre(3)
            ->setTitre("Frais par type d'acte");
        $manager->persist($groupe8);


        $parent1 = new ModuleParent();
        $parent1->setTitre('NOTARI');
        $parent1->setOrdre(2);
        $parent1->setActive(1);
        $manager->persist($parent1);



        $mod_ = new Module();
        $mod_->setTitre('Gestion agenda');
        $mod_->setOrdre(2);
        $mod_->setActive(1);
        $mod_->setIcon($icon);
        $mod_->setParent($parent1);
        $manager->persist($mod_);

        $groupe_1 = new Groupe();
        $groupe_1->setIcon($icon1)
            ->setLien('agenda')
            ->setModule($mod_)
            ->setOrdre(1)
            ->setTitre('agenda');
        $manager->persist($groupe_1);

        $groupe_2 = new Groupe();
        $groupe_2->setIcon($icon1)
            ->setLien('calendar')
            ->setModule($mod_)
            ->setOrdre(2)
            ->setTitre('Liste des événements');
        $manager->persist($groupe_2);


        $mod1 = new Module();
        $mod1->setTitre('Gestion des courriers');
        $mod1->setOrdre(3);
        $mod1->setActive(1);
        $mod1->setIcon($icon);
        $mod1->setParent($parent1);
        $manager->persist($mod1);

        $groupe = new Groupe();
        $groupe->setIcon($icon1)
            ->setLien('courierInterne')
            ->setModule($mod1)
            ->setOrdre(3)
            ->setTitre('Courriers Internes');
        $manager->persist($groupe);

        $groupe1 = new Groupe();
        $groupe1->setIcon($icon1)
            ->setLien('courierArrive')
            ->setModule($mod1)
            ->setOrdre(1)
            ->setTitre('Courriers arrivés');
        $manager->persist($groupe1);

        $groupe2 = new Groupe();
        $groupe2->setIcon($icon1)
            ->setLien('courierDepart')
            ->setModule($mod1)
            ->setOrdre(2)
            ->setTitre('Courriers départs');
        $manager->persist($groupe2);

        $mod = new Module();
        $mod->setTitre('Gestion des actes');
        $mod->setOrdre(1);
        $mod->setActive(1);
        $mod->setIcon($icon);
        $mod->setParent($parent1);
        $manager->persist($mod);

        $groupe4 = new Groupe();
        $groupe4->setIcon($icon1)
            ->setLien('client')
            ->setModule($mod)
            ->setOrdre(1)
            ->setTitre('Identification des clients');
        $manager->persist($groupe4);

        $groupe5 = new Groupe();
        $groupe5->setIcon($icon1)
            ->setLien('acte')
            ->setModule($mod)
            ->setOrdre(2)
            ->setTitre('Les actes');
        $manager->persist($groupe5);
        $user1 = new User();
        $password = "achi";
        $user1->setPassword($this->encode->hashPassword($user1, $password));
        $user1->setActive(1);
        $user1->setNom("Achi");
        $user1->setPrenoms("Achi");
        $user1->setEmail("achi@gmail.com");
        $manager->persist($user1);








 /*       $groupe3 = new Groupe();
        $groupe3->setIcon($icon1)
            ->setLien('type')
            ->setModule($mod2)
            ->setOrdre(1)
            ->setTitre('TypeActe acte');
        $manager->persist($groupe3);*/



          $user = new User();

          $user->setNom('Konate')
              ->setemail('konatenhamed@gmail.com')
              ->setPrenoms('Hamed')
              ->setPassword($this->encode->hashPassword($user, "konate"))
              ->setActive(1);
        $manager->persist($user);
        /*  $mod = new Module();
          for ($i = 1; $i <= 2000; $i++) {
           $group[$i] = new Groupe();
           $group[$i]->setIcon("menu-bullet menu-bullet-line");
              $group[$i]->setOrdre(1);
              $group[$i]->setLien('parent');
             // $group[$i]->setModule($mod);
              $group[$i]->setTitre('parent');
              $manager->persist($group[$i]);
          }*/


        // $manager->persist($user);

        $manager->flush();
    }
}
