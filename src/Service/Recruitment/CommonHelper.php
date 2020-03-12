<?php

namespace App\Service\Recruitment;

use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class CommonHelper
{

    public function setHire($relation)
    {
        // set apply state 
        if($relation->getHired() == false && $relation->getFinished() == false) {
            $relation->setHired(true);
            $relation->setFinished(false);
            $relation->setRefused(false);
            $relation->setDateRecruit(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        }
    }

    public function setAgree($relation)
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

    public function setConfirm($relation)
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

    public function setRecruitFinish($relation)
    {
        if($relation->getHired() == false 
            &&  $relation->getAgree() == true 
            // && $relation->getConfirmed() == true
            && $relation->getFinished() == false 
            && $relation->getRefused() == false 
        ) {
            $relation->setHired(false);
            $relation->setAgree(false);
            // $relation->setConfirmed(false);
            $relation->setFinished(true);
            $relation->setRefused(false);
            $relation->setDateFinished(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        }
    }

    public function setApplyFinish($relation)
    {
        if($relation->getHired() == false 
            &&  $relation->getAgree() == false 
            && $relation->getConfirmed() == true
            && $relation->getFinished() == false 
            && $relation->getRefused() == false 
        ) {
            $relation->setHired(false);
            $relation->setAgree(false);
            $relation->setConfirmed(false);
            $relation->setFinished(true);
            $relation->setRefused(false);
            $relation->setDateFinished(new \DateTime('now', new DateTimeZone('Europe/Paris')));
        }
    }

    public function setRefuse($relation)
    {
        $relation->setHired(false);
        $relation->setFinished(false);
        $relation->setRefused(true);
    }
}




