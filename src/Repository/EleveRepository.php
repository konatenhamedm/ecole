<?php

namespace App\Repository;

use App\Entity\Annee;
use App\Entity\AnneeHasClasse;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Parcours;
use App\Entity\Scolarite;
use App\Entity\Versement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Eleve>
 *
 * @method Eleve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eleve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eleve[]    findAll()
 * @method Eleve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EleveRepository extends ServiceEntityRepository
{
    use TableInfoTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eleve::class);
    }

    public function add(Eleve $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Eleve $entity, bool $flush = false): void
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
SELECT COUNT(t.id), t.matricule
FROM eleve as t
WHERE  1 = 1
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['matricule']);



        $stmt = $connection->executeQuery($sql, $params);


        return intval($stmt->fetchOne());
    }



    public function getAll($limit, $offset, $searchValue = null)
    {
        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $sql = <<<SQL
SELECT t.id, t.matricule,t.nom,t.prenoms,t.observations,t.description,t.naissance,t.genre
FROM eleve t
WHERE  1 = 1
SQL;
        $params = [];

        $sql .= $this->getSearchColumns($searchValue, $params, ['matricule']);

        $sql .= ' ORDER BY matricule';

        if ($limit && $offset == null) {
            $sql .= " LIMIT {$limit}";
        } else if ($limit && $offset) {
            $sql .= " LIMIT {$offset},{$limit}";
        }



        $stmt = $connection->executeQuery($sql, $params);
        return $stmt->fetchAllAssociative();
    }
    /*SELECT e.`matricule`,e.`nom`,s.scolarite_personne -SUM(v.montant)  AS reste_non_affecte,a.scolarite - SUM(v.montant)  AS reste,a.scolarite,s.scolarite_personne,
    SUM(v.montant) - 5000 AS paye,c.libelle
    FROM `eleve` AS e
    INNER JOIN scolarite AS s ON s.`eleve_id` = e.`id`
    INNER JOIN versement AS v ON v.scolarite_id=s.id
    INNER JOIN annee_has_classe AS a ON  s.ahc_id =a.id
    INNER JOIN classe AS c ON a.classe_id = c.id AND c.libelle = "2"

    GROUP BY e.`matricule`*/

    public function getListe($id)
    {
        return $this->createQueryBuilder('e')
            ->select(
                's.scolaritePersonne',
                's.scolaritePersonne-SUM(v.montant) as reste_non_affecte',
                'a.scolarite',
                'a.scolarite - SUM(v.montant)   AS reste_affecte',
                'SUM(v.montant) AS paye',
                'e.nom',
                'e.prenoms',
                'e.statut',
                'e.naissance',
                'e.matricule',
                'p.libelle'

            )
            ->innerJoin(Scolarite::class,'s','WITH','s.eleve=e.id')
            ->innerJoin(AnneeHasClasse::class,'a','WITH','s.ahc=a.id')
            ->innerJoin(Versement::class,'v','WITH','v.scolarite=s.id')
            ->innerJoin(Classe::class,'c','WITH','a.classe=c.id')
            ->innerJoin(Parcours::class,'p','WITH','c.parcours=p.id')
            ->andWhere('c.id = :val')
            ->addGroupBy('e.matricule')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Eleve[] Returns an array of Eleve objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Eleve
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
