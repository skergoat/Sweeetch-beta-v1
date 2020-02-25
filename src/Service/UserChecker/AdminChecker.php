<?php

namespace App\Service\UserChecker;

use App\Repository\ApplyRepository;
use App\Repository\OffersRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminChecker
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

    public function isSuperAdmin() 
    {
        return $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN'); 
    }

    // general 
    public function Exception()
    {
        throw new AccessDeniedException('Accès refusé');
    }

    // Admin 
    public function adminValid($admin)
    {   
        return $this->isSuperAdmin() or $this->user->getRoles() == $admin->getRoles() ? true : $this->Exception();
    }

}