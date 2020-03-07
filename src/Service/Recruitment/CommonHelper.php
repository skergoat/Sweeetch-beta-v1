<?php

namespace App\Service\Recruitment;

// use App\Repository\RecruitRepository;
// use Symfony\Component\HttpFoundation\Session\SessionInterface;
// use Symfony\Component\Security\Core\Security;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CommonHelper
{

    public function hire($relation)
    {
        // set apply state 
        if($relation->getHired() == false && $relation->getConfirmed() == false) {
            $relation->setHired(true);
            $relation->setConfirmed(false);
            $relation->setRefused(false);
        }
    }

    public function agree($relation)
    {
        // set apply state 
        if($relation->getHired() == true 
            && $relation->getConfirmed() == false 
            && $relation->getRefused() == false 
            && $relation->getAgree() == false
        ) {
            $relation->setHired(false);
            $relation->setConfirmed(false);
            $relation->setRefused(false);
            $relation->setAgree(true);
        }
    }

    public function confirm($relation)
    {
        if($relation->getHired() == false 
            && $relation->getConfirmed() == false 
            && $relation->getRefused() == false 
            &&  $relation->getAgree() == true 
        ) {
            $relation->setHired(false);
            $relation->setConfirmed(true);
            $relation->setRefused(false);
            $relation->setAgree(false);
        }
    }

    public function refuse($relation)
    {
        $relation->setHired(false);
        $relation->setConfirmed(false);
        $relation->setRefused(true);
    }
}




