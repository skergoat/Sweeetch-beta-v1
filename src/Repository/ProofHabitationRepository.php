<?php

namespace App\Repository;

use App\Entity\ProofHabitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProofHabitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProofHabitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProofHabitation[]    findAll()
 * @method ProofHabitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProofHabitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProofHabitation::class);
    }

    // /**
    //  * @return ProofHabitation[] Returns an array of ProofHabitation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProofHabitation
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
