<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
        $user->setRoles(['ROLE_SUPER_STUDENT']); 
        
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/sendwarning/{id}", name="sendwarning", methods={"POST"})
     */
    public function sendmail(MailerInterface $mailer, User $user, Request $request)
    {
        if($user->getRoles() == ['ROLE_SUPER_STUDENT']) {
            $user->setRoles(['ROLE_STUDENT']); 
            $this->getDoctrine()->getManager()->flush();
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

        $parameters['message'] != '' ? $message = $parameters['message'] : $message = '';
       
        // $mail = (new Email())
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $user->getStudent()->getName()))
            ->subject('Problems with docs')
            ->htmlTemplate('email/warning.html.twig')
            ->context([
                'message' => $message,
                'array' => $array
            ]); 
            // ->html($message);
        
        $mailer->send($mail);

        return $this->redirectToRoute('admin');
        
    }

}
