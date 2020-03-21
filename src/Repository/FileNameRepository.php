<?php

namespace App\Repository;

use App\Entity\FileName;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FileName|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileName|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileName[]    findAll()
 * @method FileName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileName::class);
    }
}
