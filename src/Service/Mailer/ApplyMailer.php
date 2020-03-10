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

    public function sendApplyNotification($offers)
    {
        $email = $offers->getCompany()->getUser()->getEmail();
        $name = $offers->getCompany()->getFirstname();
        $title = $offers->getTitle();

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

    public function sendHireNotification($apply)
    {
        $email = $apply->getStudent()->getUser()->getEmail();
        $name = $apply->getStudent()->getName();
        $title = $apply->getOffers()->getTitle(); 

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

    public function sendOtherNotification($others)
    {   
        $title = $others->getOffers()->getTitle();
        $name = $others->getStudent()->getName();
        $email = $others->getStudent()->getUser()->getEmail();

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

    public function sendAgreeNotification($student, $offers)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $offers->getTitle();

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

    public function sendConfirmNotification($student, $offers)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $offers->getTitle(); 

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

    public function sendFinishNotification($student, $offers)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $offers->getTitle(); 

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

    public function sendRefuseNotification($student, $offers)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $offers->getTitle(); 

        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/refuse.html.twig')
            ->context([
                'title' => $title,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendDeleteNotification($offers)
    {
        $email = $offers->getCompany()->getUser()->getEmail();
        $name =  $offers->getCompany()->getFirstName();
        $title = $offers->getTitle();

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

    //
    public function sendDeleteCompanyMessage($email, $name, $offerTitle)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/delete-company.html.twig')
            ->context([
                'title' => $offerTitle,
            ]); 
        
        $this->mailer->send($mail);
    }

    public function sendDeleteOffersCompanyMessage($student, $offers)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $offerTitle = $offers->getTitle();

        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Problems with docs')
            ->htmlTemplate('email/apply/delete-company.html.twig')
            ->context([
                'title' => $offerTitle,
            ]); 
        
        $this->mailer->send($mail);
    }

}