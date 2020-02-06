<?php

namespace App\Repository;

use App\Entity\StudentReferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StudentReferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentReferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentReferences[]    findAll()
 * @method StudentReferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentReferencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentReferences::class);
    }

    // /**
    //  * @return StudentReferences[] Returns an array of StudentReferences objects
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
    public function findOneBySomeField($value): ?StudentReferences
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
