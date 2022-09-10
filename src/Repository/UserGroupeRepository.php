<?php

namespace App\Repository;

use App\Entity\UserGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroupe[]    findAll()
 * @method UserGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroupe::class);
    }

    // /**
    //  * @return UserGroupe[] Returns an array of UserGroupe objects
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
    public function findOneBySomeField($value): ?UserGroupe
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
