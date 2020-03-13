<?php 

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ForgottenMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRecoverPassword($user, $url)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($user->getEmail()))
            ->subject('Mot de passe oublié')
            ->htmlTemplate('email/recover.html.twig')
            ->context([
                'url' => $url,
            ]); 
        
        $this->mailer->send($mail);
    }
}