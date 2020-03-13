<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\AdminHelper;
use App\Service\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
// use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminConfirmController extends AbstractController
{   
    /**
     * @Route("admin/confirm/{id}/{from}", name="admin_confirm", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function confirm($from, User $user, UserRepository $userRepository, Request $request, AdminHelper $helper): Response
    {    
        if($this->isCsrfTokenValid('confirm'.$user->getId(), $request->request->get('_token'))) 
        { 
            // confirm
            $helper->confirm($user); 
            // save 
            $this->getDoctrine()->getManager()->flush();
            // send flash  
            $this->addFlash('success', 'Compte Confirmé');
            // redirect
            if($from == 'admin' || $from == 'student_index' || $from == 'company_index' || $from == 'school_index') {
                return $this->redirectToRoute($from);
            }
            else {
                throw new \Exception('La route demandée n\'existe pas');
            } 
        }
        else {
            throw new \Exception('Demande Invalide');
        }
    }

    /**
     * @Route("/warning/{from}/{id}", name="warning", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function sendWarning(Mailer $mailer, User $user, Request $request, $from)
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

        dd($this->isCsrfTokenValid('warning'.$this->getUser()->getId(), $request->request->get('_token')));

        if($this->isCsrfTokenValid('warning'.$this->getUser()->getId(), $request->request->get('_token'))) {
            $mailer->sendWarningMessage($name, $email, $array, $message);
        }
        else {
            throw new \Exception('Demande Invalide');
        }

        $this->addFlash('success', 'Message Envoyé');

        if($from == 'admin' || $from == 'student_index' || $from == 'company_index' || $from == 'school_index') {
            return $this->redirectToRoute($from);
        }
        else {
            throw new \Exception('La route demandée n\'existe pas');
        }  
        
    }

}
