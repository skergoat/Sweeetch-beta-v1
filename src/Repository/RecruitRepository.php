<?php

namespace App\Repository;

use App\Entity\Recruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Recruit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recruit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recruit[]    findAll()
 * @method Recruit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recruit::class);
    }

    // /**
    //  * @return Recruit[] Returns an array of Recruit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recruit
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
