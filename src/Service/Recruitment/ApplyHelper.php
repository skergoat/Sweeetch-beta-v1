<?php

namespace App\Service\Recruitment;

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
    public function checkHired($key, $student)
    {
        return $this->applyRepository->findBy([$key => $student, 'hired' => 1]);
    }

    public function checkAgree($key, $student)
    {
        return $this->applyRepository->findBy([$key => $student, 'agree' => 1]);
    }
 
    public function checkConfirmed($key,$student)
    {
        return  $this->applyRepository->findBy([$key => $student, 'confirmed' => 1]);
    }

    public function checkFinished($key, $student)
    {
        return $this->applyRepository->findBy([$key => $student, 'finished' => 1]);
    }

    public function checkApply($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student]);
        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }

    public function checkRefused($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student, 'refused' => true]);
    }

    // unavailable
    public function unavailables($offers, $student)
    {
        $unavailables = $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true) {
                $unavailables->setUnavailable(true);
                
                if($unavailables->getHired() == true) {
                    $unavailables->setHired(false);
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

    // public function sendNotification($offers)
    // {
    //     $email = $offers->getCompany()->getUser()->getEmail();
    //     $name = $offers->getCompany()->getFirstname();
    //     $title = $offers->getTitle();
        
    //     $this->mailer->sendApplyMessage($email, $name, $title);
    // }
}