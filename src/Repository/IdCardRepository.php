<?php

namespace App\Repository;

use App\Entity\IdCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method IdCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdCard[]    findAll()
 * @method IdCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdCard::class);
    }

    // /**
    //  * @return IdCard[] Returns an array of IdCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IdCard
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
