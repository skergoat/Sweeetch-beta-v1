<?php

namespace App\Service;

// use App\Entity\Apply;
// use App\Entity\Offers;
// use App\Entity\Student;
// use App\Repository\ApplyRepository;
// use App\Service\Mailer\ApplyMailer;
use App\Entity\User;
// use App\Service\Recruitment\CommonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminHelper
{
    // private $applyRepository; 
    // private $mailer;
    // private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        // $this->applyRepository = $applyRepository;
        // $this->mailer = $mailer;
        $this->manager = $manager;
    }

    public function confirm(User $user)
    {
        $user->getActivateToken() == true ? $token = false : $token = true;

        dd($token);

        if($user->getStudent() != null)
        {
            $token == true ? $user->setRoles(['ROLE_SUPER_STUDENT']) : $user->setRoles(['ROLE_STUDENT ROLE_WAIT']); 
        }
        else if($user->getCompany() != null) {

            $user->setRoles(['ROLE_SUPER_COMPANY']); 
        }
        else if($user->getSchool() != null)
        {
            $user->setRoles(['ROLE_SUPER_SCHOOL']); 
        }
               
    }

}