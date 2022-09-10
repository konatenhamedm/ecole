<?php

namespace App\Repository;

use App\Entity\Annee;
use App\Entity\AnneeHasClasse;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Module;
use App\Entity\ModuleParent;
use App\Entity\Scolarite;
use App\Entity\Versement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Scolarite>
 *
 * @method Scolarite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scolarite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scolarite[]    findAll()
 * @method Scolarite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScolariteRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scolarite::class);
    }

    public function add(Scolarite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Scolarite $entity, bool $flush = false): void
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

    public function countAll($searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $sql = <<<SQL
SELECT COUNT(t.id), e.matricule,a.scolarite,t.scolarite_personne
FROM scolarite AS t,eleve e,annee_has_classe a
WHERE  t.eleve_id = e.id AND t.ahc_id = a.id AND t.scolarite_personne = 0 AND 1 = 1 
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['matricule','scolarite_personne']);

        $stmt = $connection->executeQuery($sql, $params);


        return intval($stmt->fetchOne());
    }

    public function countAllNon($searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $sql = <<<SQL
SELECT COUNT(t.id), e.matricule,a.scolarite,t.scolarite_personne
FROM scolarite AS t,eleve e,annee_has_classe a
WHERE  t.eleve_id = e.id AND t.ahc_id = a.id AND t.scolarite_personne > 0 AND 1 = 1 
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['matricule','scolarite_personne']);

        $stmt = $connection->executeQuery($sql, $params);


        return intval($stmt->fetchOne());
    }

    public function getAll($limit, $offset, $searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = <<<SQL
SELECT t.id,e.matricule,a.scolarite,t.scolarite_personne ,t.scolarite_personne-SUM(v.`montant`)+ 5000 as reste_non_affecte ,a.scolarite - SUM(v.`montant`) + 5000  AS reste,SUM(v.`montant`) - 5000 AS paye
FROM scolarite AS t,eleve e,annee_has_classe a,versement v
WHERE  t.eleve_id = e.id AND t.ahc_id = a.id AND t.id = v.scolarite_id AND t.scolarite_personne = 0 AND 1 = 1
GROUP BY t.id 
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['matricule','scolarite_personne']);

        $sql .= ' ORDER BY id';

        if ($limit && $offset == null) {
            $sql .= " LIMIT {$limit}";
        } else if ($limit && $offset) {
            $sql .= " LIMIT {$offset},{$limit}";
        }



        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAllAssociative();
    }

    public function getAllNon($limit, $offset, $searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = <<<SQL
SELECT t.id,e.matricule,a.scolarite,t.scolarite_personne ,t.scolarite_personne-SUM(v.`montant`)+ 5000 as reste_non_affecte ,a.scolarite - SUM(v.`montant`) + 5000  AS reste,SUM(v.`montant`) - 5000 AS paye
FROM scolarite AS t,eleve e,annee_has_classe a,versement v
WHERE  t.eleve_id = e.id AND t.ahc_id = a.id AND t.id = v.scolarite_id AND t.scolarite_personne > 0 AND 1 = 1
GROUP BY t.id 
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['matricule','scolarite_personne']);

        $sql .= ' ORDER BY id';

        if ($limit && $offset == null) {
            $sql .= " LIMIT {$limit}";
        } else if ($limit && $offset) {
            $sql .= " LIMIT {$offset},{$limit}";
        }



        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAllAssociative();
    }

    public function getEventDateValide()
    {
        $conn = $this->getEntityManager()
            ->getConnection();
        $sql = "
                    SELECT count(id)
                    FROM scolarite s
                  where NOW() = s.created_at
                    ";
        $stmt = $conn->executeQuery($sql);
        return $stmt->fetchOne();
    }

    public function getInfoEleve($id)
    {
        return $this->createQueryBuilder('s')
            ->select('s.scolaritePersonne','s.scolaritePersonne-SUM(v.montant)+ 5000 as reste_non_affecte',
                'a.scolarite - SUM(v.montant) + 5000  AS reste','a.scolarite','SUM(v.montant) - 5000 AS paye','e.nom','e.prenoms',
                'e.matricule','an.libelle'
            )
            ->innerJoin(Eleve::class,'e','WITH','s.eleve=e.id')
            ->innerJoin(AnneeHasClasse::class,'a','WITH','s.ahc=a.id')
            ->innerJoin(Versement::class,'v','WITH','v.scolarite=s.id')
            ->innerJoin(Annee::class,'an','WITH','a.annee=an.id')
            ->andWhere('s.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

//    /**
//     * @return Scolarite[] Returns an array of Scolarite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Scolarite
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
