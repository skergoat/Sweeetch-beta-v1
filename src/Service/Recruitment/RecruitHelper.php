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

    // recruit state
    public function checkHired($key, $param)
    {
        return $this->recruitRepository->findBy([$key => $param, 'hired' => 1]);
    }

    public function checkAgree($key, $param)
    {
        return $this->recruitRepository->findBy([$key => $param, 'agree' => 1]);
    }

    public function checkConfirmed($key, $param)
    {
        return  $this->recruitRepository->findBy([$key => $param, 'confirmed' => 1]);
    }

    public function checkRefused($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student, 'refused' => true]);
    }

    public function checkRecruit($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student]);
        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }
    
    // tel other recruiters that student is unavailable 
    public function unavailables($studies, $student)
    {
        $unavailables = $this->recruitRepository->setToUnavailables($studies, $student);

        foreach($unavailables as $unavailables) {
            if($unavailables->getRefused() != true && $unavailables->getAgree() != true) {
                $unavailables->setUnavailable(true);

                if($unavailables->getHired() == true) {
                    $unavailables->setHired(false);
                }
            }              
        }
    }
}