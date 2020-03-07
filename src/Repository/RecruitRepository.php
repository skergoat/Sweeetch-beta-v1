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

    public function setToUnavailables($studies, $student) { //
        return $this->createQueryBuilder('u')
            ->andWhere('u.studies != :studies AND u.student = :student')
            ->setParameter('studies', $studies)
            ->setParameter('student', $student)
            ->getQuery()
            ->getResult()
        ;
    }
}
