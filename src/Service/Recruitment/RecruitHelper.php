<?php

namespace App\Service\Recruitment;

use App\Entity\Recruit;
use App\Entity\Student;
use App\Entity\Studies;
use App\Repository\RecruitRepository;
use App\Service\Mailer\RecruitMailer;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Recruitment\CommonHelper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RecruitHelper extends CommonHelper
{
    private $recruitRepository; 
    private $mailer;
    private $manager;

    public function __construct(RecruitRepository $recruitRepository, RecruitMailer $mailer, EntityManagerInterface $manager)
    {
        $this->recruitRepository = $recruitRepository;
        $this->mailer = $mailer;
        $this->manager = $manager;
    }

    // recruit state
    public function checkHired($key, $param)
    {
        return $this->recruitRepository->findBy([$key => $param, 'hired' => 1]);
    }

    public function checkAgree($key, $param)
    {
        return $this->recruitRepository->findBy([$key => $param, 'agree' => 1]);
    }

    public function checkFinished($key, $param)
    {
        return  $this->recruitRepository->findBy([$key => $param, 'finished' => 1]);
    }

    public function checkRefused($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student, 'refused' => true]);
    }

    public function checkRecruit($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student]);
        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }
    
    // unavailable 
    public function unavailables($studies, $student)
    {
        $unavailables = $this->recruitRepository->setToUnavailables($studies, $student);

        foreach($unavailables as $unavailables) {
            if($unavailables->getRefused() != true && $unavailables->getAgree() != true) {
                $unavailables->setUnavailable(true);

                if($unavailables->getHired() == true) {
                    $unavailables->setHired(false); 
                }
            }              
        }
    }

    // available
    public function available($studies, $student)
    {
         $unavailables =  $this->recruitRepository->setToUnavailables($studies, $student);
 
         foreach($unavailables as $unavailables) {
             if($unavailables->getUnavailable() == true) {
                 $unavailables->setUnavailable(false);
             }      
         }
    }

    // delete unavailable
    public function deleteUnavailable($studies, $student)
    {
         $unavailables = $this->recruitRepository->setToUnavailables($studies, $student);
 
         foreach($unavailables as $unavailables) {
             if($unavailables->getUnavailable() == true) {
                $this->manager->remove($unavailables);
             }      
         }
    }

    public function hire(Recruit $recruit, Student $student, Studies $studies)
    {
        // set state
        $this->setHire($recruit);
        // send notification
        $this->mailer->sendHireNotification($recruit);
    }

    public function agree(Recruit $recruit, Student $student, Studies $studies)
    {    
        // agree
        $this->setAgree($recruit);
        // set to unavailable
        $this->unavailables($studies, $student);
        // send notification
        $this->mailer->sendAgreeNotification($student, $studies);
    }

    public function finish(Recruit $recruit, Student $student, Studies $studies)
    {
         // confirm
         $this->setRecruitFinish($recruit);
         // delete unavailables
         $this->deleteUnavailable($studies, $student);
         // set roles 
         $user = $recruit->getStudent()->getUser();
         $user->setRoles(['ROLE_SUPER_STUDENT']);
         // send notification
         $this->mailer->sendFinishNotification($student, $studies);
         // set to available
         $this->available($studies, $student);
    }

    public function refuse(Recruit $recruit, Student $student, Studies $studies)
    {
        // refuse
        $this->setRefuse($recruit);
        // send notification
        $this->mailer->sendRefuseNotification($student, $studies);     
    }

}