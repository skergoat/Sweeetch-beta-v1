<?php

namespace App\Service\Recruitment;

use DateTimeZone;

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
        if($relation->getHired() == false && $relation->getFinished() == false) {
            $relation->setHired(true);
            $relation->setFinished(false);
            $relation->setRefused(false);
            $relation->setDateRecruit(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        }
    }

    public function agree($relation)
    {
        // set apply state 
        if($relation->getHired() == true 
            && $relation->getFinished() == false 
            && $relation->getRefused() == false 
            && $relation->getAgree() == false
        ) {
            $relation->setHired(false);
            $relation->setFinished(false);
            $relation->setRefused(false);
            $relation->setAgree(true);
            $relation->setDateRecruit(new \DateTime('now', new DateTimeZone('Europe/Paris')));
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
            $relation->setDateFinished(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        }
    }

    public function finish($relation)
    {
        if($relation->getHired() == false 
            // && $relation->getConfirmed() == true
            && $relation->getFinished() == false 
            && $relation->getRefused() == false 
            &&  $relation->getAgree() == false 
        ) {
            $relation->setHired(false);
            $relation->setFinished(true);
            // $relation->setConfirmed(false);
            $relation->setRefused(false);
            $relation->setAgree(false);
            $relation->setDateFinished(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        }
    }

    public function refuse($relation)
    {
        $relation->setHired(false);
        $relation->setFinished(false);
        $relation->setRefused(true);
    }
}




