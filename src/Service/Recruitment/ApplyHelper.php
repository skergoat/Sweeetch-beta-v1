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
        return $this->findByOffersFinished($offers);
    }

    public function checkRefused($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student, 'refused' => true]);
    }

    public function checkUnavailable($offers, $student)
    {
        return $this->applyRepository->findBy(['offers' => $offers, 'student' => $student, 'unavailable' => true]);
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
            'finished' => false,
            // 'wait' => false
        ], ['date_recruit' => 'desc']);
    }

    // unavailable
    public function unavailables($offers, $student)
    {
        $unavailables = $this->applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true && $unavailables->getAgree() != true && $unavailables->getFinished() != true) {
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

    // get applies from company 
    public function findByOffersFinished($offers)
    {
        $applies = $this->applyRepository->findBy(['offers' => $offers], ['date_finished' => 'desc']);

        foreach($applies as $applies){
            if($applies->getConfirmed() || $applies->getFinished()) {
                $array[] = $applies;
            }
        }

        return isset($array) ? $array : null;
    }
    
    // apply 
    public function hire(Apply $apply, Student $student, Offers $offers)
    {
        // hire
        $this->setHire($apply);
        // close offer 
        $offers->setState(true);
        // send notification
        $content = "<p>Bonne nouvelle ! Vous avez été sélectionné pour l'offre : <strong>".$offers->getTitle()."</strong>.</p><br><p>Nous vous invitons à accepter ou refuser l'embauche.</p><br>";
        // $this->mailer->sendHireNotification($apply, $content);
        $this->mailer->sendAppliesNotification(
            $apply->getStudent()->getUser()->getEmail(), 
            $apply->getStudent()->getName(),
            $content
        );
        // delete other applies
        $others = $this->applyRepository->getOtherApplies($student->getId(), $offers->getId());
        if($others) {
            foreach($others as $others) {
                // send notification
                $content = " <p>La mission <strong>".$offers->getTitle()."</strong> a laquelle vous aviez postulé, a été attribuée à quelqu'un d'autre.</p><br>
                <p>Ne vous decouragez pas et continuez vos recherches, vous trouverez forcément quelque chose !</p><br>";
                // $this->mailer->sendOtherNotification($others);
                $this->mailer->sendAppliesNotification(
                    $others->getStudent()->getUser()->getEmail(), 
                    $others->getStudent()->getName(),
                    $content
                );
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
         $content = "<p>L'Etudiant à qui vous avez proposé l'offre : <strong> ".$offers->getTitle()."</strong> vient de l'accepter.</p><br><p>Nous vous invitons à prendre contact avec lui au plus vite pour signer les contrats d'embauche.</p><br>";
         $this->mailer->sendAppliesNotification(
            $offers->getCompany()->getUser()->getEmail(),
            $student->getName(), 
            $content
        );
         // set to unavailable
         $this->unavailables($offers, $student);
    }

    public function confirm(Apply $apply, Student $student, Offers $offers)
    {
        // confirm
        $this->setConfirm($apply);
        // send notification
        $content = '<p>Bonne nouvelle ! L\'entreprise <strong>'.$offers->getCompany()->getCompanyName().'</strong> a confirmé vous avoir recruté sur l\'offre <strong>'.$offers->getTitle().'</strong>.</p><br><p>Vous pouvez désormais chercher une école en cliquant sur l\'onglet "Formations" de votre compte Sweeetch.</p><br>';
        // $this->mailer->sendConfirmNotification($student, $offers, $content);
        $this->mailer->sendAppliesNotification(
            $student->getUser()->getEmail(),
            $student->getName(), 
            $content
        );
        // set roles
        $student->getUser()->setRoles(['ROLE_STUDENT_HIRED']);
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

    // when student has school 
    public function finish(Apply $apply, Student $student, ?Offers $offers)
    {
        if($offers == null) {
            // delete applies if offers is null 
            $this->manager->remove($apply);
        }
        else {
            // finish 
            $this->setApplyFinish($apply);
        }   
        // delete unavailables 
        $this->deleteUnavailable($offers, $student); 
    }

    public function refuse(Apply $apply, Student $student, Offers $offers)
    {
         // refuse
         $this->setRefuse($apply);
         // close offer 
        //  $offers->setState(false);
         // send notification
         $content = "<p>Vous aviez postulé sur l'offre : <strong>".$offers->getTitle()."</strong></p>
         <p>Malheureusement l'entreprise n'a pas donné suite à votre demande.</p><br>
         <p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelque chose !</p><br>";
        //  $this->mailer->sendRefuseNotification($student, $offers, $content);
        $this->mailer->sendAppliesNotification(
            $student->getUser()->getEmail(),
            $student->getName(), 
            $content
        );
         // set to available
         // $helper->available($offers, $student);
    }

    public function delete(Apply $apply, Student $student, ?Offers $offers)
    {
        // close offer 
        if($offers != null) {
            $offers->setState(false);
            $content = "<p>L'Etudiant que vous aviez sélectionné sur l'offre : <strong>".$offers->getTitle()."</strong> n'a pas donné suite.</p><br><p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelqu'un !</p><br>";
            // $this->mailer->sendDeleteNotification($offers, $content);
            $this->mailer->sendAppliesNotification(
                $offers->getCompany()->getUser()->getEmail(),
                $offers->getCompany()->getFirstName(), 
                $content
            );
        }    
    }

    // when delete offers 
    public function handleOffersApplies(Offers $offers)
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
            $content = "<p>Malheureusement l'offre <strong>".$offers->getTitle()."</strong> à laquelle vous aviez postulé a été supprimée par l'entreprise qui l'avait publiée.</p><br><p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelque chose !</p><br>";
            // $this->mailer->sendDeleteOffersCompanyMessage($student, $offers, $content);
            $this->mailer->sendAppliesNotification(
                $student->getUser()->getEmail(),
                $student->getName(), 
                $content
            );
            // remove unfinished applies and set offers_id to null
            if($applies->getFinished() == false) {
                $this->manager->remove($applies);
            }
            else {
                $applies->setOffers(NULL);
            }
        }
    }

    // when company delete profile 
    public function handleCompanyApplies($company)
    {
        // get related offers 
        $offers = $company->getOffers();

        foreach($offers as $offers) {
            $applies = $offers->getApplies();
            // get related applies 
            foreach($applies as $applies) {
                $student = $applies->getStudent();
                // send mail 
                $content = "<p>Malheureusement l'offre <strong>".$offers->getTitle()."</strong> à laquelle vous aviez postulé a été supprimée par l'entreprise qui l'avait publiée.</p><br><p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelque chose !</p><br>";
                // $this->mailer->sendDeleteCompanyMessage($student, $offers, $content);
                $this->mailer->sendAppliesNotification(
                    $student->getUser()->getEmail(),
                    $student->getName(), 
                    $content
                );
                // if applies is agree, allow student to look for another job 
                if($this->checkConfirmed('offers', $offers) == []) {
                    $this->available($applies->getOffers(), $applies->getStudent());
                }

                // delete offers or keep finished or confirmed applies  
                if($this->checkConfirmed('offers', $offers) == [] && $this->checkFinished('offers', $offers) == []) {
                    // remove related applies 
                    $this->manager->remove($applies);
                }
                else {
                    $applies->setOffers(NULL);
                } 
            }
            // remove related offers 
            $this->manager->remove($offers);
        }
    }

    // when delete company profile 
    public function handleStudentApplies($student)
    {
        // get applies 
        $applies = $student->getApplies();
                
        foreach($applies as $applies) {
            // get offers 
            $offers = $applies->getOffers();
            // send mail 
            $content = "<p>Malheureusement l'étudiant <strong>".$student->getName()." ".$student->getLastName()."</strong> que vous souhaitiez recruté a supprimé son compte.</p><br>
            <p>Nous nous excusons pour ce contretemps et espérons que vous trouverez rapidement quelqu'un d'autre pour le remplacer</p><br>";
            // $this->mailer->sendDeleteStudentMessage($student, $offers, $content); 
            $this->mailer->sendAppliesNotification(
                $offers->getCompany()->getUser()->getEmail(),
                $offers->getCompany()->getFirstName(), 
                $content
            );

            if($this->checkConfirmed('student', $student) == [] && $this->checkFinished('student', $student) == []) {
                // set offers state 
                $offers->setState(false);
                // delete applies 
                $this->manager->remove($applies);
            }
            else {
                // set student_id to null
                $applies->setStudent(NULL);
                // delete unavailables
                $this->deleteUnavailable($offers, $student);
            } 
        }
    }
}