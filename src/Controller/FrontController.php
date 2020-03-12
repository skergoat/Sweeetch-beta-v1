<?php

namespace App\Controller;

use App\Invitation\SendInviteMail;
use App\Service\Mailer\InviteMailer;
use App\Service\Mailer\ContactMailer;
use App\Repository\InvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render("Front/index.html.twig");
    }

    /**
     * @Route("/conditions", name="conditions")
     */
    public function conditions()
    {
        return $this->render("Front/conditions.html.twig");
    }


     /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        return $this->render("Front/Faq.html.twig");
    }
    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request,  ContactMailer $mailer)
    {
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
            
        $mailer->send($email, $name, $subject, $message);

        return new Response('OK');
    }

    //  /**
    //  * @Route("/invite", name="invite")
    //  * @IsGranted("ROLE_ADMIN")
    //  */
    // public function invitation(InvitationRepository $repository,  InviteMailer $mailer) 
    // {
    //     $mail = $repository->findAll();

    //     foreach($mail as $mail)
    //     {
    //         $mailer->invite($mail->getEmails());
    //     }

    //     $this->addFlash('success', 'Invitations envoyées');

    //     return $this->redirectToRoute('admin');
    // }

     /**
     * @Route("/invite", name="invite")
     * @IsGranted("ROLE_ADMIN")
     */
    public function invite(MessageBusInterface $bus, InviteMailer $mailer)
    {
        // or use the shortcut
        // $this->dispatchMessage(new SendInviteMail('sfsfsdfs'));
        $mailer->invite('infos-dev@sweeetch.com');

        $this->addFlash('success', 'Invitations envoyées');

        return $this->redirectToRoute('admin');
    }
}
