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

    /**
     * @Route("/sendwarning/{id}", name="sendwarning", methods={"POST"})
     */
    public function sendmail(User $user, Request $request)
    {
        if($user->getRoles() == 'ROLE_SUPER_STUDENT') {
            $user->setRoles(['ROLE_STUDENT']); 
            $this->getDoctrine()->getManager()->flush();
        }

        $parameters = $request->request->all();
       
        $email = $parameters['email'];

        $message = 'Bonjour, <br/><br/> Il semblerait que les documents suivants ne soient pas valides : <ul>';

        if($parameters['resume'] != NULL) {
            $message .= '<li><strong>CV</strong></li>';
        }

        if($parameters['idCard'] != NULL) {
            $message .= '<li><strong>Carte d\'identite</strong></li>';
        }

        if($parameters['studentCard'] != NULL) {
            $message .= '<li><strong>Carte d\'etudiant</strong></li>';
        }

        if($parameters['proofHabitation'] != NULL) {
            $message .= '<li><strong>Justificatif de domicile</strong></li>';
        }

        $message .= 
        ' 
            </ul>
            <p>Pour benficier de toutes les fonctionnalites de votre comptes, il est tres important que vous mettiez ces documents a jours.</p>
            <p> Cordialement,</p> 
            <p><strong>L\'equipe de Sweetch</strong></p>
        ';

        if($message != '') {
            $message .= '<br/><h4>Note personnelle :</h4> <p>' . $parameters['message'] . '</p>';
        }

        return new Response($email . '<br><br>' . $message);
        
    }

}
