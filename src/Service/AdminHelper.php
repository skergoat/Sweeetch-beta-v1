<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminHelper
{
    private $manager;
    private $mailer;

    public function __construct(EntityManagerInterface $manager, Mailer $mailer)
    {
        $this->manager = $manager;
        $this->mailer = $mailer;
    }

    public function confirm(User $user)
    {
        $user->getActivateToken() == true ? $token = true : $token = false;

        if($user->getStudent() != null)
        {
            if($token == false)
            {
                $user->setRoles(['ROLE_SUPER_STUDENT']); 
                $this->mailer->sendConfirmed($user->getEmail(), $user->getStudent()->getName(), $user->getStudent()->getLastName());
            }
        }
        else if($user->getCompany() != null) {

            if($token == false)
            {
                $user->setRoles(['ROLE_SUPER_COMPANY']);
                $this->mailer->sendConfirmed($user->getEmail(), $user->getCompany()->getFirstName(), $user->getCompany()->getLastName()); 
            }
        }
        else if($user->getSchool() != null)
        {
            if($token == false)
            {
                $user->setRoles(['ROLE_SUPER_SCHOOL']);
                $this->mailer->sendConfirmed($user->getEmail(), $user->getSchool()->getFirstName(), $user->getSchool()->getLastName());
            } 
        }           
    }

    public function activateAccount($user)
    {
        $user->getConfirmed() == true ? $confirmed = true : $confirmed = false;

        if($user->getStudent() != null)
        {
            if($confirmed == true)
            {
                $user->setRoles(['ROLE_SUPER_STUDENT']); 
                $this->mailer->sendConfirmed($user->getEmail(), $user->getStudent()->getName(), $user->getStudent()->getLastName());
            }
        }
        else if($user->getCompany() != null) {

            if($confirmed == true)
            {
                $user->setRoles(['ROLE_SUPER_COMPANY']);
                $this->mailer->sendConfirmed($user->getEmail(), $user->getCompany()->getFirstName(), $user->getCompany()->getLastName());  
            }
        }
        else if($user->getSchool() != null)
        {
            if($confirmed == true)
            {
                $user->setRoles(['ROLE_SUPER_SCHOOL']);
                $this->mailer->sendConfirmed($user->getEmail(), $user->getSchool()->getFirstName(), $user->getSchool()->getLastName());
            } 
        }
    }
}