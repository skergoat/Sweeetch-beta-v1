<?php

namespace App\Repository;

use App\Entity\StudentCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StudentCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentCard[]    findAll()
 * @method StudentCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentCard::class);
    }

    // /**
    //  * @return StudentCard[] Returns an array of StudentCard objects
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
    public function findOneBySomeField($value): ?StudentCard
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
