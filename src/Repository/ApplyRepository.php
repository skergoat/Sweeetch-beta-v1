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


    public function findAppliedIfExists($student, $offer)
    {
        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.student = :student AND u.offers = :offer AND u.hired = :hired OR u.agree = :agree OR u.confirmed = :confirmed OR u.finished = :finished')
        ->setParameter('student', $student)
        ->setParameter('offer', $offer)
        ->setParameter('hired', true)
        ->setParameter('agree', true)
        ->setParameter('confirmed', true)
        ->setParameter('finished', true)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function checkIfOpen($offers) 
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.hired = :hired OR u.agree = :agree OR u.confirmed = :confirmed OR u.finished = :finished')
            ->setParameter('offers', $offers)
            ->setParameter('hired', true)
            ->setParameter('agree', true)
            ->setParameter('confirmed', true)
            ->setParameter('finished', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function checkIfHired($student) // 
    { 
        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.student = :student AND u.hired = :hired')
        ->setParameter('student', $student->getId())
        ->setParameter('hired', true)
        ->getQuery()
        ->getOneOrNullResult();
    }


    public function findByStudentByFresh($student) 
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->andWhere('u.student = :student AND u.refused = :refused AND u.unavailable = :unavailable AND u.hired = :hired AND u.agree = :agree AND u.confirmed = :confirmed AND u.finished = :finished')
            ->setParameter('student', $student)
            ->setParameter('refused', false)
            ->setParameter('unavailable', false)
            ->setParameter('hired', false)
            ->setParameter('agree', false)
            ->setParameter('confirmed', false)
            ->setParameter('finished', false)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getHired()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.hired = :hired OR u.agree = :agree')
            ->setParameter('hired', true)
            ->setParameter('agree', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getStudentHired()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.agree = :agree')
            ->setParameter('agree', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function checkIfFinished($offers) // 
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.finished = :finished')
            ->setParameter('offers', $offers)
            ->setParameter('finished', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByOffer($offer) {  // 
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

    public function findByOfferByFinished($offer) { // 
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers = :offers AND u.finished = :finished')
            ->setParameter('offers', $offer)
            ->setParameter('finished', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByStudentByFinished($student) // 
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.student = :student AND u.finished = :finished')
            ->setParameter('student', $student)
            ->setParameter('finished', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByStudent($student) { // 
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

    public function setToUnavailables($offers, $student) { //
        return $this->createQueryBuilder('u')
            ->andWhere('u.offers != :offers AND u.student = :student')
            ->setParameter('offers', $offers)
            ->setParameter('student', $student)
            ->getQuery()
            ->getResult()
        ;
    }

    public function checkIfRowExsists($offers, $student) { // 

        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.offers = :offers AND u.student = :student')
        ->setParameter('offers', $offers->getId())
        ->setParameter('student', $student->getId())
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function checkIfrefusedExsists($offers, $student) { //
        return (boolean)$this->createQueryBuilder('u')
        ->andWhere('u.offers = :offers AND u.student = :student AND u.refused = :refused')
        ->setParameter('offers', $offers->getId())
        ->setParameter('student', $student->getId())
        ->setParameter('refused', true)
        ->getQuery()
        ->getOneOrNullResult();
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
