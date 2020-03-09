<?php

namespace App\Service\Recruitment;

use App\Entity\Apply;
use App\Repository\ApplyRepository;
use App\Service\Recruitment\CommonHelper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ApplyHelper extends CommonHelper
{
    private $recruitRepository; 
    private $session;

    public function __construct(ApplyRepository $applyRepository, SessionInterface $session)
    {
        $this->applyRepository = $applyRepository;
        $this->session = $session;
    }

    // check apply state 
    public function checkHired($key, $param)
    {
        return $this->applyRepository->findBy([$key => $param, 'hired' => 1]);
    }

    public function checkAgree($key, $param)
    {
        $this->applyRepository->findBy([$key => $param, 'agree' => 1]);
    }
 
    public function checkConfirmed($key,$param)
    {
        return  $this->applyRepository->findBy([$key => $param, 'confirmed' => 1]);
    }

    public function checkFinished($key, $student)
    {
        return $this->applyRepository->findBy([$key => $student, 'finished' => 1]);
    }

    public function checkRefused($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student, 'refused' => true]);
    }

    public function checkApply($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student]);
        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }

    // unavailable
    public function unavailables($offers, $student)
    {
        $unavailables = $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true && $unavailables->getAgree() != true) {
                $unavailables->setUnavailable(true);
                
                if($unavailables->getHired() == true && $unavailables->getOffers()->getState() == true) {
                    $unavailables->setHired(false);
                    $unavailables->getOffers()->setState(false);
                }
            }  
        }
    }

    // available
    public function available($offers, $student)
    {
        $unavailables =  $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {
            if($unavailables->getUnavailable() == true) {
                $unavailables->setUnavailable(false);
            }      
        }
    }

    // end sweeetch process 
    public function endProcess(Apply $apply)
    {
        // finish 
        $this->finish($apply);
        // set roles 
        $user = $apply->getStudent()->getUser();
        $user->setRoles(['ROLE_SUPER_STUDENT']); 
        // send notification
        // $mailer->sendFinishNotification($student, $offers);
        // set to available
        $student = $apply->getStudent();
        $offers = $apply->getOffers();
        $this->available($offers, $student);
    }

}