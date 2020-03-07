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

    public function checkRecruit($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student]);

        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }
    
    public function checkRefused($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student, 'refused' => true]);
    }

    public function checkAgree($student)
    {
        return $this->recruitRepository->findBy(['student' => $student, 'agree' => 1]);
    }

    public function checkConfirmed($student)
    {
        return  $this->recruitRepository->findBy(['student' => $student, 'confirmed' => 1]);
    }
    
    // tel other recruiters that student is unavailable 
    public function unavailables($studies, $student)
    {
        $unavailables = $this->recruitRepository->setToUnavailables($studies, $student);

        foreach($unavailables as $unavailables) {

            // if($bool == true) {
                if($unavailables->getRefused() != true) {
                    $unavailables->setUnavailable(true);
                } 
            // }
            // elseif($bool == false) {
            //     if($unavailables->getUnavailable() == true) {
            //         $unavailables->setUnavailable(false);
            //     }
            // }
             
        }
    }
}