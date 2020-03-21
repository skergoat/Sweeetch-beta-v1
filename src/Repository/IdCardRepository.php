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
}
