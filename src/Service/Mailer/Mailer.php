<?php 

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWarningMessage($name, $email, $array, $message)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problème avec les documents')
            ->htmlTemplate('email/warning.html.twig')
            ->context([
                'message' => $message,
                'array' => $array
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendConfirmed($email, $firstName, $lastName)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email))
            ->subject('Confirmation de compte')
            ->htmlTemplate('email/confirmed.html.twig')
            ->context([
                'firstName' => $firstName,
                'lastName' => $lastName
            ]); 
        
        $this->mailer->send($mail);
    }
}