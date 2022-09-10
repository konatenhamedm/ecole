<?php

namespace App\Repository;

use App\Entity\AnneeHasClasse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnneeHasClasse>
 *
 * @method AnneeHasClasse|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnneeHasClasse|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnneeHasClasse[]    findAll()
 * @method AnneeHasClasse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnneeHasClasseRepository extends ServiceEntityRepository
{

    use TableInfoTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnneeHasClasse::class);
    }

    public function add(AnneeHasClasse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AnneeHasClasse $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countAll($searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $sql = <<<SQL
SELECT COUNT(t.id), t.scolarite,classe_id,annee_id
FROM annee_has_classe as t
WHERE  1 = 1
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['scolarite']);



        $stmt = $connection->executeQuery($sql, $params);


        return intval($stmt->fetchOne());
    }



    public function getAll($limit, $offset, $searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = <<<SQL
SELECT t.id, t.description,t.observations,t.scolarite,classe_id,c.libelle as classe_id,a.libelle as annee_id
FROM annee_has_classe t,classe c,annee a
WHERE  t.classe_id = c.id and t.annee_id = a.id and 1 = 1
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['scolarite']);

        $sql .= ' ORDER BY scolarite';

        if ($limit && $offset == null) {
            $sql .= " LIMIT {$limit}";
        } else if ($limit && $offset) {
            $sql .= " LIMIT {$offset},{$limit}";
        }



        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAllAssociative();
    }

//    /**
//     * @return AnneeHasClasse[] Returns an array of AnneeHasClasse objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnneeHasClasse
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
