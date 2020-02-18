<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ApplyRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BackController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository, ApplyRepository $applyRepository)
    {
        $students = $userRepository->findByRole('ROLE_STUDENT');
        $company = $userRepository->findByRole('ROLE_COMPANY');
        $school = $userRepository->findByRole('ROLE_SCHOOL');

        $applies = $applyRepository->getHired();
        
        return $this->render('back/index.html.twig', [
            'students' => $students,
            'company' => $company,
            'school' => $school,
            'applies' => $applies
        ]);
    }
}
