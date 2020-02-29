<?php

namespace App\Controller;

use App\Service\Mailer\ContactMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request,  ContactMailer $mailer) 
    {
        $email = $request->request->get('email');
        $name = $request->request->get('name');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
    
        $mailer->send($email, $name, $subject, $message);

        return $this->redirectToRoute('homepage');
    }

}
