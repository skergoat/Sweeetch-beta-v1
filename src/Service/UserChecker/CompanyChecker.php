<?php

namespace App\Service\UserChecker;

use App\Repository\ApplyRepository;
use App\Repository\OffersRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CompanyChecker
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

    public function isAdmin() 
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN'); 
    }

    public function Exception(){
        throw new AccessDeniedException('Accès refusé');
    }

    // company 
    public function companyValid($company)
    {   
        $userRequired = $company->getUser()->getId();
        return $this->isAdmin() or $this->user->getId() == $userRequired ? true : $this->Exception();
    }

     // student show single apply  
    public function companyOffersValid($company, $offers)
    {    
         $userRequired = $company->getUser()->getId();
 
         if($this->isAdmin() 
         || $this->user->getId() == $userRequired 
         AND $this->offersRepository->offerExists($company, $offers)) 
         {
             return true;
         }
         else {
             $this->Exception() ;
         }
    }

    public function studentProfileValid($company, $offers, $student) 
    {
        $userRequired = $company->getUser()->getId();

        if($this->isAdmin()
        || $this->user->getId() == $userRequired 
        AND $this->offersRepository->offerExists($company, $offers)
        AND $this->applyRepository->applyExists($student, $offers))
        {
            return true;
        }
        else {
            $this->Exception() ;
        }

    }

}