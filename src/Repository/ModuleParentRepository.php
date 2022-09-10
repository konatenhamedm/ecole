<?php

namespace App\Repository;

use App\Entity\ModuleParent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModuleParent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleParent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleParent[]    findAll()
 * @method ModuleParent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleParentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleParent::class);
    }

    // /**
    //  * @return ModuleParent[] Returns an array of ModuleParent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModuleParent
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
