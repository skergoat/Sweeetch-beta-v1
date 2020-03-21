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
}
