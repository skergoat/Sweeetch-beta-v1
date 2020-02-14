<?php 

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ApplyMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendApplyMessage($email, $name, $title)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/apply.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendHireMessage($email, $name, $title)
    {
        // dd('send');

        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/hire.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendOthersMessage($email, $name, $title)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/others.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendAgreeMessage($email, $name, $title)
    {
        // dd('send');

        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/agree.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendConfirmMessage($email, $name, $title)
    {
        // dd('send');

        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/confirm.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendFinishMessage($email, $name, $title)
    {
        // dd('send');

        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/finish.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendDeleteMessage($email, $name, $title)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/delete.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

}