<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findSearch(SearchData $search): array
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.campus', 'c');
           // ->join('p.participants', 'u');

        if(!empty($search->q)){
            $query = $query
                ->andWhere('p.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%" );
        }

        if(!empty($search->campus)){
            $query = $query
                ->andWhere('c.id IN (:campus)')
                ->setParameter('campus', $search->campus);
        }

        if(!empty($search->min)){
            $query = $query
                ->andWhere('p.dateHeureDebut >= :min')
                ->setParameter('min', $search->min );
        }

        if(!empty($search->max)){
            $query = $query
                ->andWhere('p.dateHeureDebut <= :max')
                ->setParameter('max', $search->max );
        }

        if(!empty($search->isOrga)){
            $query = $query
                ->andWhere('p.organisateur.id = :user')
                ->setParameter('user', app.user.id);
        }

        if(!empty($search->isInscrit)){
            $query = $query
                ->andWhere('p.isOrga <= 1');
        }

        if(!empty($search->isNotInscrit)){
            $query = $query
                ->andWhere('p.isOrga <= 1');
        }

        if(!empty($search->sortiesPassees)){
            $query = $query
                ->andWhere('p.etat = 5');
        }

        return $query->getQuery()->getResult();
    }
    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
