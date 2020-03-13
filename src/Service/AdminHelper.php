<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminHelper
{
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function confirm(User $user)
    {
        $user->getActivateToken() == true ? $token = true : $token = false;

        if($user->getStudent() != null)
        {
            if($token == false)
            {
                $user->setRoles(['ROLE_SUPER_STUDENT']); 
            }
        }
        else if($user->getCompany() != null) {

            if($token == false)
            {
                $user->setRoles(['ROLE_SUPER_COMPANY']); 
            }
        }
        else if($user->getSchool() != null)
        {
            if($token == false)
            {
                $user->setRoles(['ROLE_SUPER_SCHOOL']);
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
            }
        }
        else if($user->getCompany() != null) {

            if($confirmed == true)
            {
                $user->setRoles(['ROLE_SUPER_COMPANY']); 
            }
        }
        else if($user->getSchool() != null)
        {
            if($confirmed == true)
            {
                $user->setRoles(['ROLE_SUPER_SCHOOL']);
            } 
        }
    }
}