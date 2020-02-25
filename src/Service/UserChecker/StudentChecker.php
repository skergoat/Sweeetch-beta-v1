<?php

namespace App\Service\UserChecker;

use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\StudentCard;
use App\Entity\ProofHabitation;
use App\Repository\ApplyRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class StudentChecker
{
    private $authorizationChecker;
    private $user; 
    private $applyRepository;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Security $security, ApplyRepository $applyRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->user = $security->getUser();
        $this->applyRepository = $applyRepository;
    }

    // general 

    public function isAdmin() 
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN'); 
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

            case $document instanceof ProofHabitation : 
                $get = 'getProofHabitation';
            break;
        }

        return $this->isAdmin() or $this->user->getStudent()->$get()->getId() == $document->getId() ? true : $this->Exception();
       
    }
}