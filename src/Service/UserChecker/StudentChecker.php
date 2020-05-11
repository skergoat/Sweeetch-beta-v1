<?php

namespace App\Service\UserChecker;

use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\StudentCard;
// use App\Entity\ProofHabitation;
use App\Repository\ApplyRepository;
use App\Repository\OffersRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class StudentChecker
{
    private $authorizationChecker;
    private $user; 
    private $applyRepository;
    private $offersRepository;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Security $security, ApplyRepository $applyRepository, OffersRepository $offersRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->user = $security->getUser();
        $this->applyRepository = $applyRepository;
        $this->offersRepository = $offersRepository;
    }

    // general 

    public function isAdmin() {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN'); 
    }

    public function isCompany() {
        return $this->authorizationChecker->isGranted('ROLE_SUPER_COMPANY'); 
    }

    public function isSchool() {
        return $this->authorizationChecker->isGranted('ROLE_SUPER_SCHOOL'); 
    }

    public function isStudent() {
        return $this->authorizationChecker->isGranted('ROLE_SUPER_STUDENT') || $this->authorizationChecker->isGranted('ROLE_STUDENT'); 
    }

    public function Exception(){
        throw new AccessDeniedException('Accès refusé');
    }

    // student 
    public function studentValid($student)
    {   
        $userRequired = $student->getUser()->getId();
        return $this->isAdmin() or $this->user->getId() == $userRequired ? true : $this->Exception();
    }

    // student edit profile 
    public function studentProfileValid($student, $profile)
    {    
        $userRequired = $student->getUser()->getId();

        if($this->isAdmin() 
        || ($this->user->getId() == $userRequired 
        AND $this->user->getStudent()->getProfile()->getId() == $profile->getId())) {
            return true;
        }
        else {
            $this->Exception() ;
        }
    }

    // student show single apply  
    public function studentApplyValid($student, $offers)
    {    
        $userRequired = $student->getUser()->getId();

        if($this->isAdmin() 
        || $this->user->getId() == $userRequired 
        AND $this->applyRepository->applyExists($student, $offers)) {
            return true;
        }
        else {
            $this->Exception() ;
        }
    }

    public function applyValid($apply)
    {
        return $this->isAdmin() or $this->applyRepository->applyExists($this->user->getStudent(), $apply->getOffers()) ? true : $this->Exception();
    }

    // student documents 
    public function documentValid($document)
    {
        if($this->isStudent()) {

            switch($document) {
                case $document instanceof Resume : 
                    $get = 'getResume';
                break;

                case $document instanceof IdCard : 
                    $get = 'getIdCard';
                break;

                case $document instanceof StudentCard : 
                    $get = 'getStudentCard';
                break;

                // case $document instanceof ProofHabitation : 
                //     $get = 'getProofHabitation';
                // break;
            }

            return $this->isAdmin() or $this->user->getStudent()->$get()->getId() == $document->getId() ? true : $this->Exception(); 
    
        }
        else if($this->isCompany())
        {
            return $this->isAdmin() ? true : $this->Exception(); 
        }
        else if($this->isSchool())
        {
            return $this->isAdmin() ? true : $this->Exception(); 
        }
        else {
            return $this->Exception(); 
        }
    }
}