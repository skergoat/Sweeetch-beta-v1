<?php 

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class RecruitMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendRecruitNotification($studies)
    {
        $email = $studies->getSchool()->getUser()->getEmail();
        $name = $studies->getSchool()->getFirstname();
        $title = $studies->getTitle();

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

    public function sendHireNotification($recruit)
    {
        $email = $recruit->getStudent()->getUser()->getEmail();
        $name = $recruit->getStudent()->getName();
        $title = $recruit->getStudies()->getTitle(); 

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

    // public function sendOtherNotification($others)
    // {   
    //     $title = $others->getOffers()->getTitle();
    //     $name = $others->getStudent()->getName();
    //     $email = $others->getStudent()->getUser()->getEmail();

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply/others.html.twig')
    //         ->context([
    //             'title' => $title,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    public function sendAgreeNotification($student, $studies)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $studies->getTitle();

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

    // public function sendConfirmNotification($student, $offers)
    // {
    //     $email = $student->getUser()->getEmail();
    //     $name = $student->getName();
    //     $title = $offers->getTitle(); 

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply/confirm.html.twig')
    //         ->context([
    //             'title' => $title,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    public function sendFinishNotification($student, $studies)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $studies->getTitle(); 

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

    public function sendRefuseNotification($student, $studies)
    {
        $email = $student->getUser()->getEmail();
        $name = $student->getName();
        $title = $studies->getTitle(); 

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

    public function sendDeleteNotification($studies)
    {
        $email = $studies->getSchool()->getUser()->getEmail();
        $name =  $studies->getSchool()->getFirstName();
        $title = $studies->getTitle();

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

    // //
    // public function sendDeleteCompanyMessage($email, $name, $offerTitle)
    // {
    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply/delete-company.html.twig')
    //         ->context([
    //             'title' => $offerTitle,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

}