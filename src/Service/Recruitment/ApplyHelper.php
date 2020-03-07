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

    public function checkRecruit($offers, $student)
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

    public function checkAgree($student)
    {
        return $this->applyRepository->findBy(['student' => $student, 'agree' => 1]);
    }

    public function checkConfirmed($student)
    {
        return  $this->applyRepository->findBy(['student' => $student, 'confirmed' => 1]);
    }

    public function unavailables($offers, $student)
    {
        $unavailables = $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true) {
                $unavailables->setUnavailable(true);
            }  
        }
    }
}