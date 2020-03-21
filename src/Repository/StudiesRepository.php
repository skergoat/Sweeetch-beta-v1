<?php

namespace App\Repository;

use App\Entity\Studies;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Studies|null find($id, $lockMode = null, $lockVersion = null)
 * @method Studies|null findOneBy(array $criteria, array $orderBy = null)
 * @method Studies[]    findAll()
 * @method Studies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Studies::class);
    }
}
