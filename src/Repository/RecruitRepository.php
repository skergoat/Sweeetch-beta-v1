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

    public function findByStudentByFresh($student) 
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.student = :student AND u.refused = :refused AND u.unavailable = :unavailable AND u.hired = :hired AND u.agree = :agree AND u.finished = :finished')
            ->setParameter('student', $student)
            ->setParameter('refused', false)
            ->setParameter('unavailable', false)
            ->setParameter('hired', false)
            ->setParameter('agree', false)
            ->setParameter('finished', false)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function setToUnavailables($studies, $student) {

        if($student == null)
        {
            return $this->createQueryBuilder('u')
                ->andWhere('u.studies != :studies')
                ->setParameter('studies', $studies)
                // ->setParameter('student', $student)
                ->getQuery()
                ->getResult()
            ;
        }
        else {
            return $this->createQueryBuilder('u')
                ->andWhere('u.studies != :studies AND u.student = :student')
                ->setParameter('studies', $studies)
                ->setParameter('student', $student)
                ->getQuery()
                ->getResult()
            ;
        }
    }

    // display recuit in process 
    public function findProcessing($study)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.studies = :studies AND (f.hired = :hired OR f.agree = :agree)')
            ->setParameter('studies', $study)
            ->setParameter('hired', true)
            ->setParameter('agree', true)
            ->addOrderBy('f.agree', 'desc')
            ->addOrderBy('f.date_recruit', 'desc')
            ->getQuery()
            ->getResult();
    }
}
