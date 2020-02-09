<?php 

namespace App\Service;

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
            ->subject('Problems with docs')
            ->htmlTemplate('email/warning.html.twig')
            ->context([
                'message' => $message,
                'array' => $array
            ]); 
        
        $this->mailer->send($mail);
    }
}