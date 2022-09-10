<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\ModuleParent;
use App\Entity\Scolarite;
use App\Entity\Versement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Versement>
 *
 * @method Versement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Versement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Versement[]    findAll()
 * @method Versement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersementRepository extends ServiceEntityRepository
{

    use TableInfoTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versement::class);
    }

    public function add(Versement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Versement $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNombre(){
        return $this->createQueryBuilder("a")
            ->select("count(a.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getImprimer($value)
    {
        return $this->createQueryBuilder('g')
            ->select('g.titre','m.titre','p.titre')
            ->innerJoin(Module::class,'m','WITH','g.module=m.id')
            ->innerJoin(ModuleParent::class,'p','WITH','m.parent=p.id')
            ->andWhere('v.exampleField = :val')
           ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }


    public function getAllVersementBySolarite($id)
    {
        return $this->createQueryBuilder('v')
            ->select('v.libelle','v.montant','v.numeroEtape','v.dateVersement','s.scolaritePersonne')
            ->innerJoin(Scolarite::class,'s','WITH','v.scolarite=s.id')
            ->andWhere('s.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
            ;
    }


//    /**
//     * @return Versement[] Returns an array of Versement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Versement
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
