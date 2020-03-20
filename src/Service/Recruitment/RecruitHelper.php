<?php

namespace App\Service\Recruitment;

use App\Entity\School;
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

    public function checkUnavailable($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student, 'unavailable' => true]);
    }

    public function checkRecruit($studies, $student)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'student' => $student]);
        // if($already) {
        //     $this->session->getFlashBag()->add('error', 'Vous avez déjà postulé');
        // }
    }

    public function nbCandidates($studies)
    {
        return $this->recruitRepository->findBy(['studies' => $studies, 'refused' => 0, 'unavailable' => 0, 'finished' => 0]);
    }

    // has no job but has school
    // public function hasRecruit($student)
    // {
    //     return $this->recruitRepository->findBy([
    //         'student' => $student,
    //         'hired' => false,
    //         'agree' => false,
    //         'refused' => false,
    //         'unavailable' => false,
    //         'finished' => false
    //     ]);
    // }
    
    // unavailable 
    public function unavailables($studies, $student)
    {
        $unavailables = $this->recruitRepository->setToUnavailables($studies, $student);

        foreach($unavailables as $unavailables) {
            if($unavailables->getRefused() != true && $unavailables->getAgree() != true && $unavailables->getFinished() != true) {
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
        $content = "<p>Bonne nouvelle ! Vous avez été sélectionné pour le cursus : <strong>".$studies->getTitle()."</strong>.</p><br><p>Nous vous invitons à accepter ou refuser la proposition.</p><br>";
        // $this->mailer->sendHireNotification($recruit, $content);
        $this->mailer->sendAppliesNotification(
            $recruit->getStudent()->getUser()->getEmail(),
            $recruit->getStudent()->getName(), 
            $content
        );
    }

    public function agree(Recruit $recruit, Student $student, Studies $studies)
    {    
        // agree
        $this->setAgree($recruit);
        // set to unavailable
        $this->unavailables($studies, $student);
        // send notification
        $content = "<p>L'Etudiant à qui vous avez proposé le cursus : <strong> ".$studies->getTitle()."</strong> vient d'accepter votre proposition.</p><br><p>Nous vous invitons à prendre contact avec lui au plus vite pour remplir les papiers d'inscription.</p><br>";
        // $this->mailer->sendAgreeNotification($student, $studies, $content);
        $this->mailer->sendAppliesNotification(
            $studies->getSchool()->getUser()->getEmail(),
            $student->getName(), 
            $content
        );
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
         $content = "<p>Félicitations, votre parcours de recrutement Sweeetch est terminé !</p><br><p>Vous allez désormais voler de vos propres ailes... et vous allez nous manquer !</p><br>";
        //  $this->mailer->sendFinishNotification($student, $studies, $content);
        $this->mailer->sendAppliesNotification(
            $student->getUser()->getEmail(),
            $student->getName(), 
            $content
        );
         // set to available
        //  $this->available($studies, $student);
    }

    public function refuse(Recruit $recruit, Student $student, Studies $studies)
    {
        // refuse
        $this->setRefuse($recruit);
        // send notification
        // $this->mailer->sendRefuseNotification($student, $studies);
        $content = "<p>Vous aviez postulé sur l'offre : <strong>".$studies->getTitle()."</strong></p>
        <p>Malheureusement l'entreprise n'a pas donné suite à votre demande.</p><br>
        <p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelque chose !</p><br>";     
        $this->mailer->sendAppliesNotification(
            $student->getUser()->getEmail(),
            $student->getName(), 
            $content
        );
    }

    public function handleDeleteRecruit(Studies $studies, School $school)
    {  
        if($this->checkHired('studies', $studies)) {
            // set available
            $this->available($studies, null);
            // entities
            $recruits = $this->recruitRepository->findBy(['studies' => $studies]);

            foreach($recruits as $recruits) {
                // get student
                $student = $recruits->getStudent();
                // send mail 
                $content = "<p>Malheureusement l'offre <strong>".$studies->getTitle()."</strong> à laquelle vous aviez postulé a été supprimée par l'entreprise qui l'avait publiée.</p><br><p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelque chose !</p><br>";
                // $this->mailer->sendDeleteOffersCompanyMessage($student, $offers, $content);
                $this->mailer->sendAppliesNotification(
                    $student->getUser()->getEmail(),
                    $student->getName(),  
                    $content
                );
            }   
        }

        // if recruit > mail notif if remove studies ?
        // set to null and ask student to continue research or to stop ? 
    }

    public function handleDeleteSchool(School $school)
    { 
        $studies = $school->getStudies();

        foreach($studies as $studies) 
        {
            if($this->checkHired('studies', $studies)) {
                $this->available($studies, null);

                $recruits = $this->recruitRepository->findBy(['studies' => $studies]);

                foreach($recruits as $recruits) {
                    // get student
                    $student = $recruits->getStudent();
                    // send mail 
                    $content = "<p>Malheureusement l'offre <strong>".$studies->getTitle()."</strong> à laquelle vous aviez postulé a été supprimée par l'entreprise qui l'avait publiée.</p><br><p>Ne vous découragez pas et continuez votre recherche : vous finirez bien par trouver quelque chose !</p><br>";
                    // $this->mailer->sendDeleteOffersCompanyMessage($student, $offers, $content);
                    $this->mailer->sendAppliesNotification(
                        $student->getUser()->getEmail(),
                        $student->getName(),  
                        $content
                    );
                } 
            }
        }

        // if recruit > mail notif if remove studies ?
        // set to null and ask student to continue research or to stop ? 
    }
}