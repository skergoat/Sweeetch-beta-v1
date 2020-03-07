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

    public function checkIfAlreadyRecruit($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student]);

        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
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