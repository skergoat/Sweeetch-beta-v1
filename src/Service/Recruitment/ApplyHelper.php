<?php

namespace App\Service\Recruitment;

use App\Entity\Apply;
use App\Entity\Offers;
use App\Entity\Student;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Recruitment\CommonHelper;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ApplyHelper extends CommonHelper
{
    private $applyRepository; 
    private $mailer;
    private $manager;

    public function __construct(ApplyRepository $applyRepository, ApplyMailer $mailer, EntityManagerInterface $manager)
    {
        $this->applyRepository = $applyRepository;
        $this->mailer = $mailer;
        $this->manager = $manager;
    }

    // check state and infos 
    public function checkHired($key, $param)
    {
        return $this->applyRepository->findBy([$key => $param, 'hired' => 1]);
    }

    public function checkAgree($key, $param)
    {
        return $this->applyRepository->findBy([$key => $param, 'agree' => 1]);
    }
 
    public function checkConfirmed($key,$param)
    {
        return  $this->applyRepository->findBy([$key => $param, 'confirmed' => 1]);
    }

    public function checkFinished($key,$param)
    {
        return  $this->applyRepository->findBy([$key => $param, 'finished' => 1]);
    }

    public function checkOfferFinished($offers)
    {
        return $this->applyRepository->findByOffersFinished($offers);
    }

    public function checkRefused($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student, 'refused' => true]);
    }

    public function checkApply($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student]);
    }
    
    public function nbCandidates($offers)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'refused' => 0, 'unavailable' => 0, 'confirmed' => 0, 'finished' => 0]);
    }

    // open applies 
    public function checkApplies($key, $param)
    {
        return $this->applyRepository->findBy([
            $key => $param,
            'hired' => false,
            'agree' => false,
            'refused' => false,
            'unavailable' => false,
            'confirmed' => false,
            'finished' => false
        ]);
    }

    // unavailable
    public function unavailables($offers, $student)
    {
        $unavailables = $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true && $unavailables->getAgree() != true) {
                $unavailables->setUnavailable(true);
                
                if($unavailables->getHired() == true && $unavailables->getOffers()->getState() == true) {
                    $unavailables->setHired(false);
                    $unavailables->getOffers()->setState(false);
                }
            }  
        }
    }

    // available
    public function available($offers, $student)
    {
        $unavailables =  $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {
            if($unavailables->getUnavailable() == true) {
                $unavailables->setUnavailable(false);
            }      
        }
    }

     // delete unavailable
     public function deleteUnavailable($offers, $student)
     {
          $unavailables = $this->applyRepository->setToUnavailables($offers, $student);
  
          foreach($unavailables as $unavailables) {
              if($unavailables->getUnavailable() == true) {
                 $this->manager->remove($unavailables);
              }      
          }
     }
    
    // apply 
    public function hire(Apply $apply, Student $student, Offers $offers)
    {
        // hire
        $this->setHire($apply);
        // close offer 
        $offers->setState(true); 
        // send notification
        $this->mailer->sendHireNotification($apply);
        // delete other applies
        $others = $this->applyRepository->getOtherApplies($student->getId(), $offers->getId());
        if($others) {
            foreach($others as $others) {
                // send notification
                $this->mailer->sendOtherNotification($others);
                // delete other applies 
                $this->manager->remove($others);   
            }   
        }
    }

    public function agree(Apply $apply, Student $student, Offers $offers)
    {    
         // agree
         $this->setAgree($apply);
         // send notification
         $this->mailer->sendAgreeNotification($student, $offers);
         // set to unavailable
         $this->unavailables($offers, $student);
    }

    public function confirm(Apply $apply, Student $student, Offers $offers)
    {
        // confirm
        $this->setConfirm($apply);
        // send notification
        $this->mailer->sendConfirmNotification($student, $offers);
        // set roles
        $student->getUser()->setRoles(['ROLE_STUDENT_HIRED']);
    }

    public function finish(Apply $apply, Student $student, Offers $offers, $bool)
    {
        // finish 
        $this->setApplyFinish($apply);
        // delete unavailables
        if($bool){
            $this->deleteUnavailable($offers, $student);
        }
        else {
            // set to available
            $this->available($offers, $student);
        } 
        // set roles 
        $user = $apply->getStudent()->getUser();
        $user->setRoles(['ROLE_SUPER_STUDENT']); 
        // send notification
        $this->mailer->sendFinishNotification($student, $offers);  
    }

    public function refuse(Apply $apply, Student $student, Offers $offers)
    {
         // refuse
         $this->setRefuse($apply);
         // close offer 
        //  $offers->setState(false);
         // send notification
         $this->mailer->sendRefuseNotification($student, $offers);
         // set to available
         // $helper->available($offers, $student);
    }

    public function delete(Apply $apply, Student $student, Offers $offers)
    {
        // close offer 
        $offers->setState(false);
        // set to available
        // $helper->available($offers, $student);
        // send notification
        $this->mailer->sendDeleteNotification($offers);
    }

    public function handleApplies(Offers $offers)
    {   
        // entities
        $applies = $this->applyRepository->findBy(['offers' => $offers]);
        // handle related applies
        foreach($applies as $applies) 
        {
            $student = $applies->getStudent();
            // set to available
            $this->available($offers, $student);
            // send mail 
            $this->mailer->sendDeleteOffersCompanyMessage($student, $offers);
            // remove unfinished applies and set offers_id to null
            if($applies->getFinished() == false) {
                $this->manager->remove($applies);
            }
            else {
                $applies->setOffers(NULL);
            }
        }
    }
}