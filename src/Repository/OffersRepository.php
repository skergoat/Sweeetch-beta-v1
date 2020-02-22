<?php

namespace App\Repository;

use App\Entity\Offers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Offers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offers[]    findAll()
 * @method Offers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offers::class);
    }

    public function findAllPaginated($order = "DESC")
    {
        return $this->createQueryBuilder('p')
        ->orderBy('p.id', $order)
        ->getQuery()
        ->getResult();
    }

    public function findAllPaginatedByCompany($order = "DESC", $company)
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.company = :company')
        ->setParameter('company', $company)
        ->orderBy('p.id', $order)
        ->getQuery()
        ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Offers
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
