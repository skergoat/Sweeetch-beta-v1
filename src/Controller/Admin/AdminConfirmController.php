<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminConfirmController extends AbstractController
{
    /**
     * @Route("admin/confirm/{id}", name="admin_confirm", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function confirm(User $user, UserRepository $userRepository): Response
    {     
        if($user->getStudent() != null)
        {
            $user->setRoles(['ROLE_SUPER_STUDENT']); 
        }
        else if($user->getCompany() != null) {

            $user->setRoles(['ROLE_SUPER_COMPANY']); 
        }
        else if($user->getSchool() != null)
        {
            $user->setRoles(['ROLE_SUPER_SCHOOL']); 
        }
                
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/sendwarning/{id}", name="sendwarning", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function sendWarning(Mailer $mailer, User $user, Request $request)
    {

        if($user->getRoles() == ['ROLE_SUPER_STUDENT']) {
            $user->setRoles(['ROLE_STUDENT']); 
            $this->getDoctrine()->getManager()->flush();
        }

        else if($user->getRoles() == ['ROLE_SUPER_COMPANY']) {
            $user->setRoles(['ROLE_COMPANY']); 
            $this->getDoctrine()->getManager()->flush();
        }

        else if($user->getRoles() == ['ROLE_SUPER_SCHOOL']) {
            $user->setRoles(['ROLE_SCHOOL']); 
            $this->getDoctrine()->getManager()->flush();
        }

        if($user->getStudent() != null)
        {
            $name = $user->getStudent()->getName();
        }
        else if($user->getCompany() != null) 
        {
            $name = $user->getCompany()->getFirstname();
        }
        else if($user->getSchool() != null) 
        {
            $name = $user->getSchool()->getFirstname();
        }

        $parameters = $request->request->all();
       
        $email = $parameters['email'];
        $array = [];

        if(isset($parameters['resume']) && $parameters['resume'] != NULL) {
            $array[] = 'CV';
        }

        if(isset($parameters['idCard']) && $parameters['idCard'] != NULL) {
            $array[] ='cart d\'identite';
        }

        if(isset($parameters['studentCard']) && $parameters['studentCard'] != NULL) {
            $array[] = 'carte d\'etudiant';
        }

        if( isset($parameters['proofHabitation']) && $parameters['proofHabitation'] != NULL) {
            $array[] = 'justificatif de domicile';
        }

        if( isset($parameters['siret']) && $parameters['siret'] != NULL) {
            $array[] = 'numero de siret';
        }

        $parameters['message'] != '' ? $message = $parameters['message'] : $message = '';

        $mailer->sendWarningMessage($name, $email, $array, $message);

        return $this->redirectToRoute('admin');
        
    }

}
