<?php

namespace App\Service\Recruitment;

use App\Repository\RecruitRepository;
use App\Service\Recruitment\CommonHelper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
// use Symfony\Component\Security\Core\Security;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RecruitHelper extends CommonHelper
{
    private $recruitRepository; 
    private $session;

    public function __construct(RecruitRepository $recruitRepository, SessionInterface $session)
    {
        $this->recruitRepository = $recruitRepository;
        $this->session = $session;
    }

    public function checkIfAlreadyRecruit($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student]);

        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }  

    public function unavailables($studies, $student)
    {
        $unavailables = $this->recruitRepository->setToUnavailables($studies, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true) {
                $unavailables->setUnavailable(true);
            }  
        }
    }

    // public function checkHired($student)
    // {
    //     return $this->recruitRepository->findBy(['student' => $student, 'hired' => 1]);
    // }

    public function checkAgree($student)
    {
        return $this->recruitRepository->findBy(['student' => $student, 'agree' => 1]);
    }

    public function checkConfirmed($student)
    {
        return  $this->recruitRepository->findBy(['student' => $student, 'confirmed' => 1]);
    }  
}