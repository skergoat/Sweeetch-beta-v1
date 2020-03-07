<?php

namespace App\Service\Recruitment;

use App\Repository\RecruitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
// use Symfony\Component\Security\Core\Security;
// use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RecruitHelper
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
}