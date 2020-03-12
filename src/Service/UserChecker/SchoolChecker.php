<?php

namespace App\Service\UserChecker;

use App\Repository\RecruitRepository;
use App\Repository\StudiesRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SchoolChecker
{
    private $authorizationChecker;
    private $user; 
    private $recruitRepository;
    private $studiesRepository;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Security $security, RecruitRepository $recruitRepository, StudiesRepository $studiesRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->user = $security->getUser();
        $this->recruitRepository = $recruitRepository;
        $this->studiesRepository = $studiesRepository;
    }

    // general 

    public function isAdmin() 
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN'); 
    }

    public function Exception(){
        throw new AccessDeniedException('Accès refusé');
    }

    // school 
    public function schoolValid($school)
    {   
        $userRequired = $school->getUser()->getId();
        return $this->isAdmin() or $this->user->getId() == $userRequired ? true : $this->Exception();
    }

    // Studies edit 
    public function schoolStudiesEditValid($school, $studies)
    {    
        $userRequired = $school->getUser()->getId();

        if($this->isAdmin() 
        || $this->user->getId() == $userRequired 
        AND $this->studiesRepository->findBy(['id' => $studies->getId(),'school' => $school])) {
            return true;
        }
        else {
            $this->Exception() ;
        }
    }


}