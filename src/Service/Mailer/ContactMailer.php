<?php 

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ContactMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($mail, $name, $subject, $message)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('berkani.noam@sweeetch.com', 'Sweeetch.com'))
            ->to(new Address('infos-contacts@sweeetch.com'))
            ->subject('Prise de Contact')
            ->htmlTemplate('email/contact.html.twig')
            ->context([
                'subject' => $subject,
                'message' => $message,
                'mail' => $mail,
                'name' => $name
            ]); 
        
        $this->mailer->send($mail);
    }
}