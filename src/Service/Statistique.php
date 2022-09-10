<?php
namespace App\Service;


use App\Entity\Eleve;
use App\Entity\Scolarite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Statistique {

    private $em;


    public function __construct(EntityManagerInterface $em){

        $this->em = $em;
    }
    public function getTotalEleve(){

        $repo = $this->em->getRepository(Eleve::class)->createQueryBuilder('e');
        return $repo->select('count(e.id)')
            ->getQuery()
            ->getSingleScalarResult();


    }

    public function getTotalEleveInscritToDay(){

        $repo = $this->em->getRepository(Scolarite::class)->createQueryBuilder('s');
        return $repo->select('count(s.id)')
            ->Where("DAY(NOW()) = DAY(s.createdAt)")
            ->getQuery()
            ->getSingleScalarResult();


    }

    public function getTotalEleveInscritToMonth(){

        $repo = $this->em->getRepository(Scolarite::class)->createQueryBuilder('s');
        return $repo->select('count(s.id)')
            ->Where("MONTH( NOW()) = MONTH(s.createdAt)")
            ->getQuery()
            ->getSingleScalarResult();


    }

    public function getTotalInscritInYear(){

        $repo = $this->em->getRepository(Scolarite::class)->createQueryBuilder('s');
        return $repo->select('count(s.id)')
            ->Where("YEAR( NOW()) = YEAR(s.createdAt)")
            ->getQuery()
            ->getSingleScalarResult();


    }

    public function getTotalEleveGirl(){

        $repo = $this->em->getRepository(Eleve::class)->createQueryBuilder('e');
        return $repo->select('count(e.id)')
            ->Where("e.genre = 'F'")
            ->getQuery()
            ->getSingleScalarResult();


    }


    public function getTotalElveMan(){

        $repo = $this->em->getRepository(Eleve::class)->createQueryBuilder('e');
        return $repo->select('count(e.id)')
            ->Where("e.genre = 'M'")
            ->getQuery()
            ->getSingleScalarResult();


    }


    public function getTotalInscritByYear(){

        $repo = $this->em->getRepository(Scolarite::class)->createQueryBuilder('s');
        return $repo->select('count(s.id) as nombre',"DATE_FORMAT(s.createdAt,'%Y') as annee")
            ->groupBy('annee')
            ->getQuery()
            ->getResult();


    }
    public function chiffre_affaire_annee_bar(){

        $repo = $this->em->getRepository(Facture::class)->createQueryBuilder('f');
        return $repo->select('sum(f.montant) as montant',"DATE_FORMAT(f.date_facture,'%Y') as annee")
            ->Where("f.lettre is not null")
            ->groupBy('annee')
            ->getQuery()
            ->getResult();


    }

}