<?php 

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class UserMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNewUser($user, $entity)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($user->getEmail()))
            ->subject('Nouvel Inscrit')
            ->htmlTemplate('email/signup.html.twig')
            ->context([
                'name' => $entity->getName(),
                'mail' => $entity->getUser()->getEmail()
            ]); 
    
        $this->mailer->send($mail);
    }

    public function sendNewUsers($user, $entity)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($user->getEmail()))
            ->subject('Nouvel Inscrit')
            ->htmlTemplate('email/signup.html.twig')
            ->context([
                'name' => $entity->getfirstName(),
                'mail' => $entity->getUser()->getEmail()
            ]); 
    
        $this->mailer->send($mail);
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

    public function sendAdminPassword($user, $url)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($user->getEmail()))
            ->subject('Activation du compte')
            ->htmlTemplate('email/admin-mdp.html.twig')
            ->context([
                'url' => $url,
                'mail' => $user->getEmail()
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendActivate($user)
    {
        // dd($user->getActivateToken());
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($user->getEmail()))
            ->subject('Activation du compte Sweeetch')
            ->htmlTemplate('email/activate.html.twig')
            ->context([
                'token' => $user->getActivateToken(),
            ]); 
        
        $this->mailer->send($mail);
    }
}