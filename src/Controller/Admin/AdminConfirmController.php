<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminConfirmController extends AbstractController
{
    /**
     * @Route("admin/confirm/{id}", name="admin_confirm", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function confirm(User $user, UserRepository $userRepository): Response
    {         
        $user->setRoles(['ROLE_SUPER_STUDENT']); 
        
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin');
    }
}
