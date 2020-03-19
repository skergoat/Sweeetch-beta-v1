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

    public function sendAppliesNotification($email, $name, $content)
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
            ->to(new Address($email, $name))
            ->subject('Recrutement')
            ->htmlTemplate('email/apply.html.twig')
            ->context([
                'content' => $content,
            ]); 
        
        $this->mailer->send($mail);
    }

    // public function sendRecruitNotification($studies, $content)
    // {
    //     $email = $studies->getSchool()->getUser()->getEmail();
    //     $name = $studies->getSchool()->getFirstname();
    //     // $title = $studies->getTitle();

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply.html.twig')
    //         ->context([
    //             // 'title' => $title,
    //             'content' => $content
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    // public function sendHireNotification($recruit, $content)
    // {
    //     $email = $recruit->getStudent()->getUser()->getEmail();
    //     $name = $recruit->getStudent()->getName();
    //     // $title = $recruit->getStudies()->getTitle(); 

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply.html.twig')
    //         ->context([
    //             'content' => $content,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    // public function sendAgreeNotification($student, $studies, $content)
    // {
    //     $email = $studies->getSchool()->getUser()->getEmail();
    //     $name = $student->getName();
    //     // $title = $studies->getTitle();

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply.html.twig')
    //         ->context([
    //             'content' => $content,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    // public function sendFinishNotification($student, $studies, $content)
    // {
    //     $email = $student->getUser()->getEmail();
    //     $name = $student->getName();
    //     // $title = $studies->getTitle(); 

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply.html.twig')
    //         ->context([
    //             'content' => $content,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    // public function sendRefuseNotification($student, $studies)
    // {
    //     $email = $student->getUser()->getEmail();
    //     $name = $student->getName();
    //     $title = $studies->getTitle(); 

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply.html.twig')
    //         ->context([
    //             'title' => $title,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

    // public function sendDeleteNotification($studies)
    // {
    //     $email = $studies->getSchool()->getUser()->getEmail();
    //     $name =  $studies->getSchool()->getFirstName();
    //     $title = $studies->getTitle();

    //     $mail = (new TemplatedEmail())
    //         ->from(new Address('no-reply@sweeetch.com', 'Sweeetch\'s Team'))
    //         ->to(new Address($email, $name))
    //         ->subject('Problems with docs')
    //         ->htmlTemplate('email/apply.html.twig')
    //         ->context([
    //             'title' => $title,
    //         ]); 
        
    //     $this->mailer->send($mail);
    // }

}