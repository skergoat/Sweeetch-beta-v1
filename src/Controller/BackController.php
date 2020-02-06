<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BackController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository)
    {
        $user = $userRepository->findByRole('ROLE_STUDENT');
        
        return $this->render('back/index.html.twig', [
            'user' => $user
        ]);
    }
}
