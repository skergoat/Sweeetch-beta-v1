<?php

namespace App\Repository;

use App\Entity\Apply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Apply|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apply|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apply[]    findAll()
 * @method Apply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Apply::class);
    }

    public function findByOffer($offer) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.refused = :refused AND u.unavailable = :unavailable  AND u.finished = :finished')
            ->setParameter('offers', $offer)
            ->setParameter('refused', false)
            ->setParameter('unavailable', false)
            ->setParameter('finished', false)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByOfferByFinished($offer) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.finished = :finished')
            ->setParameter('offers', $offer)
            ->setParameter('finished', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByStudent($student) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.student = :student AND u.refused = :refused AND u.unavailable = :unavailable AND u.finished = :finished')
            ->setParameter('student', $student)
            ->setParameter('refused', false)
            ->setParameter('unavailable', false)
            ->setParameter('finished', false)
            ->getQuery()
            ->getResult()
        ;
    }

    public function setToUnavailables($offers, $student) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers != :offers AND u.student = :student')
            ->setParameter('offers', $offers)
            ->setParameter('student', $student)
            ->getQuery()
            ->getResult()
        ;
    }

    public function checkIfAppliesExsists($student) {

        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.student = :student')
        ->setParameter('student', $student->getId())
        ->getQuery()
        ->getResult();
    }

    public function checkIfRowExsists($offers, $student) {

        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.offers = :offers AND u.student = :student')
        ->setParameter('offers', $offers->getId())
        ->setParameter('student', $student->getId())
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function checkIfrefusedExsists($offers, $student) {
        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.offers = :offers AND u.student = :student AND u.refused = :refused')
        ->setParameter('offers', $offers->getId())
        ->setParameter('student', $student->getId())
        ->setParameter('refused', true)
        ->getQuery()
        ->getOneOrNullResult();
    }
  
    public function getSingleHiredRow($offers, $student)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.student = :student AND u.hired = :hired')
            ->setParameter('offers', $offers)
            ->setParameter('student', $student)
            ->setParameter('hired', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSingleConfirmedRow($offers, $student)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.student = :student AND u.confirmed = :confirmed')
            ->setParameter('offers', $offers)
            ->setParameter('student', $student)
            ->setParameter('confirmed', true)
            // ->setParameter('confirmed', null)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getOtherApplies($student, $offers)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.student != :student')
            ->setParameter('offers', $offers)
            ->setParameter('student', $student)
            ->getQuery()
            ->getResult()
        ;
    }

}
