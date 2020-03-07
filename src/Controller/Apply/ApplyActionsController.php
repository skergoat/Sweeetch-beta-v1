<?php

namespace App\Controller\Apply;

use App\Entity\Apply;
use App\Entity\Offers;
use App\Entity\Company;
use App\Entity\Student;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use App\Repository\OffersRepository;
use App\Repository\CompanyRepository;
use App\Repository\StudentRepository;
use App\Service\Recruitment\ApplyHelper;
use App\Service\UserChecker\StudentChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/apply")
 */
class ApplyActionsController extends AbstractController
{
    /**
     * @Route("/offers/{id}/student/{student_id}/page/{page}", name="apply", methods={"POST"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function apply(ApplyRepository $repository, Offers $offers, Student $student, ApplyMailer $mailer, StudentChecker $checker, Request $request, ApplyHelper $helper, $page)
    {
        // check if apply is open to current offer
        $hired = $repository->findBy(['offers' => $offers, 'hired' => 1]);
        $agree = $repository->findBy(['offers' => $offers, 'agree' => 1]);
        $confirmed = $repository->findBy(['offers' => $offers, 'confirmed' => 1]);
        $finished = $repository->findBy(['offers' => $offers, 'finished' => 1]);

        if($hired || $agree || $confirmed || $finished) {  
            $this->addFlash('error', 'Offre Indisponible');
            return $this->redirectToRoute('offers_index');
        }

        // check if student is already hired
        if($helper->checkAgree($student) || $helper->checkConfirmed($student)) {
            $this->addFlash('error', 'Vous êtes déjà embauché ailleurs. Rendez-vous sur votre profil.');
            return $this->redirectToRoute('offers_show', ['id' => $offers->getId(), 'page' => $page]);

        }

        // check if student have already applied to current offer 
        if($helper->checkRecruit($offers, $student) || $helper->checkRefused($offers, $student)) {  
            $this->addFlash('error', 'Vous avez déjà postulé');
            return $this->redirectToRoute('offers_show', ['id' => $offers->getId(), 'page' => $page]);
        }

        // send notification to company 
        // $email = $offers->getCompany()->getUser()->getEmail();
        // $name = $offers->getCompany()->getFirstname();
        // $offerTitle = $offers->getTitle();

        // $mailer->sendApplyMessage($email, $name, $offerTitle);

        $apply = new Apply; 
        $apply->setHired(false);
        $apply->setConfirmed(false);
        $apply->setRefused(false);
        $apply->setUnavailable(false);
        $apply->setFinished(false);
        $apply->setAgree(false);
        $apply->setOffers($offers);
        $apply->setStudent($student);

        if($this->isCsrfTokenValid('apply'.$student->getId(), $request->request->get('_token'))) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($apply);
            $manager->flush();
        }
        else {
            throw new \Exception('Utilisateur Invalide');
        }

        $this->addFlash('success', 'Postulation enregistrée !');

        return $this->redirectToRoute('offers_show', ['id' => $offers->getId(), 'page' => $page]);
 
    }

    /**
     * @Route("/hire/{id}", name="hire", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function hire(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer, Request $request, ApplyHelper $helper)
    {   
        // get users
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        // check if student is available
        if($helper->checkAgree($student) || $helper->checkConfirmed($student)) {
            $this->addFlash('error', 'Cet étudiant n\'est plus disponible.');
            return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);

        }

        // // check if student is available
        // $hired2 = $repository->findBy(['student' => $student, 'hired' => 1]);
        // $agree2 = $repository->findBy(['student' => $student, 'agree' => 1]);
        // $confirmed2 = $repository->findBy(['student' => $student, 'confirmed' => 1]);
        // // $finished2 = $repository->findBy(['student' => $student, 'finished' => 1]);

        // if($hired2 || $agree2 || $confirmed2) {
        //     $this->addFlash('error', 'Cet étudiant n\'est plus disponile');
        //     return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
        // }

        // hire
        $helper->hire($apply);

        // close offer 
        $offers->setState(true);

        // send notification to student 
        // $email = $apply->getStudent()->getUser()->getEmail();
        // $name = $apply->getStudent()->getName();
        // $offerTitle = $apply->getOffers()->getTitle(); 
        
        // $mailer->sendHireMessage($email, $name, $offerTitle); 

        if($this->isCsrfTokenValid('hire'.$apply->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
    
            $others = $repository->getOtherApplies($student->getId(), $offers->getId());

            if($others) {

                foreach($others as $others) {

                    // send mail to other applies 
                    // $offerTitle = $others->getOffers()->getTitle();
                    // $name = $others->getStudent()->getName();
                    // $email = $others->getStudent()->getUser()->getEmail();
            
                    // $mailer->sendOthersMessage($email, $name, $offerTitle); 

                    // delete other applies 
                    $entityManager->remove($others);   
                }   
            }
              // save 
              $entityManager->flush();
        }
        else {
            throw new \Exception('Candidature Invalide');
        }

        $this->addFlash('success', 'Elève Embauché !');
 
        return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
    }

    /**
     * @Route("/agree/{id}", name="agree", methods={"POST"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     */
    public function agree(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer, Request $request, StudentChecker $checker, ApplyHelper $helper)
    {
        // get entities
        $student = $apply->getStudent();
        $offers = $apply->getOffers();
        
        // agree
        $helper->agree($apply);

        // set to unavailable
        $helper->unavailables($offers, $student);

        // send notification to student 
        // $email = $student->getUser()->getEmail();
        // $name = $student->getName();
        // $offerTitle = $offers->getTitle(); 
        
        // $mailer->sendAgreeMessage($email, $name, $offerTitle); 

        if($this->isCsrfTokenValid('agree'.$apply->getId(), $request->request->get('_token'))) {
            $this->getDoctrine()->getManager()->flush();
        }
        else {
            throw new \Exception('Demande Invalide');
        }

        $this->addFlash('success', 'offre acceptée');

        return $this->redirectToRoute('student_apply', ['id' => $student->getId()]);
    }

    /**
     * @Route("/confirm/{id}", name="confirm", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function confirm(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer, Request $request, ApplyHelper $helper)
    {
        // confirm 
        $helper->confirm($apply);

        // get other applies
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

         // send notification to student 
        //  $email = $student->getUser()->getEmail();
        //  $name = $student->getName();
        //  $offerTitle = $offers->getTitle(); 
         
        //  $mailer->sendConfirmMessage($email, $name, $offerTitle); 

        $student->getUser()->setRoles(['ROLE_STUDENT_HIRED']);

        if($this->isCsrfTokenValid('confirm'.$apply->getId(), $request->request->get('_token'))) {

            $this->getDoctrine()->getManager()->flush();
        }
        else {
            throw new \Exception('Demande Invalide');
        }

        $this->addFlash('success', 'Mission Commencée. Bon travail !');

        return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
    }

     /**
     * @Route("/finish/{id}", name="apply_finish", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function finish(Apply $apply, ApplyRepository $applyRepository, ApplyMailer $mailer, Request $request)
    {
        $apply->setConfirmed(false);
        $apply->setFinished(true);

        // set appliant roles 
        $user = $apply->getStudent()->getUser();
        // $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']); 
        $user->setRoles(['ROLE_SUPER_STUDENT']); 

        // get other applies
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        // send notification to student 
        // $email = $student->getUser()->getEmail();
        // $name = $student->getName();
        // $offerTitle = $offers->getTitle(); 
          
        // $mailer->sendFinishMessage($email, $name, $offerTitle); 
 

        // set other student offers to available
        $unavailables = $applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getUnavailable() == true) {
                $unavailables->setUnavailable(false);
            }      
        }

        if($this->isCsrfTokenValid('stop'.$apply->getId(), $request->request->get('_token'))) {

            $this->getDoctrine()->getManager()->flush();
        }
        else {
            throw new \Exception('Demande Invalide');
        }

        $this->addFlash('success', 'Mission Terminée. Bravo !');

        return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
    }

     /**
     * @Route("/refuse/{id}", name="apply_refuse", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function refuse(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer, Request $request, ApplyHelper $helper)
    {
        // get users
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        if($apply->getRefused() == true) {
            $this->addFlash('error', 'Vous avez déjà refusé cette candidature');
            return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
        }

        // refuse
        $helper->refuse($apply);

        // set appliant roles 
        $user = $apply->getStudent()->getUser();
        // $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']);
        $user->setRoles(['ROLE_SUPER_STUDENT']);

        // close offer 
        $offers->setState(false);

        // send notification to student 
        // $email = $student->getUser()->getEmail();
        // $name = $student->getName();
        // $offerTitle = $offers->getTitle(); 
           
        // $mailer->sendRefuseMessage($email, $name, $offerTitle); 
  
        // set other student offers to available
        $unavailables = $repository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getUnavailable() == true) {
                $unavailables->setUnavailable(false);
            }      
        }

        // dd($this->isCsrfTokenValid('delete'.$apply->getId(), $request->request->get('_token')));

        if($this->isCsrfTokenValid('refuse'.$apply->getId(), $request->request->get('_token'))) {

            $this->getDoctrine()->getManager()->flush();
        }
        else {
            throw new \Exception('Demande Invalide');
        }

        $this->addFlash('success', 'Candidat refusée');
        // if($from == 'student') {
        //     $return = $this->redirectToRoute('student_apply', ['id' => $student->getId()]);
        // }
        // else if($from == 'company') {
        return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
        // }

     
    }

    /**
     * @Route("/delete/{id}", name="apply_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     * @ParamConverter("apply", options={"id" = "id"})
     */
    public function delete(Request $request, Apply $apply, ApplyRepository $repository, OffersRepository $offersRepository, CompanyRepository $companyRepository, ApplyMailer $mailer, StudentChecker $checker): Response
    {
        // if($checker->applyValid($apply)) 
        // {
            // get company to render company page 
            // $companyId = $apply->getOffers()->getCompany()->getId();

            // set appliant roles 
            $user = $apply->getStudent()->getUser();
            $user->setRoles(['ROLE_SUPER_STUDENT']); 

            $student = $apply->getStudent();
            $offers = $apply->getOffers();

            // close offer 
            $offers->setState(false);

            // set other student offers to unavailable
            $unavailables = $repository->setToUnavailables($offers, $student);

            foreach($unavailables as $unavailables) {

                if($unavailables->getUnavailable() == true) {
                    $unavailables->setUnavailable(false);
                } 
            }

            // send mail 
            // $email = $user->getEmail();
            // $name = $apply->getStudent()->getName();
            // $offerTitle = $apply->getOffers()->getTitle();

            // $mailer->sendDeleteMessage($email, $name, $offerTitle); 
        
            // delete apply 
            if ($this->isCsrfTokenValid('delete'.$apply->getId(), $request->request->get('_token'))) {

                $entityManager = $this->getDoctrine()->getManager();
                // delete relation
                $entityManager->remove($apply);
                // delete offer
                $entityManager->flush();
            }
            else {
                throw new \Exception('Candidature Invalide');
            }

            $this->addFlash('success', 'Candidature supprimée !');

            return $this->redirectToRoute('student_apply', ['id' => $student->getId()]);
        // }
    }

    /**
     * @Route("/delete/empty/student/{id}", name="delete_empty_studentSide", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     */
    public function deleteEmptyStudentSide(Request $request, Apply $apply): Response
    {   
        $student = $apply->getStudent();

        if ($this->isCsrfTokenValid('delete'.$apply->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($apply);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_finished', ['id' => $student->getId()]);
    }

    /**
     * @Route("/delete/empty/company/{id}", name="delete_empty_companySide", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function deleteEmptyCompanySide(Offers $offer, Request $request): Response 
    {
        $companyId = $offer->getCompany()->getId();

        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $applies = $offer->getApplies(); 

            foreach($applies as $applies) 
            {        
                $entityManager->remove($applies);
            }

            $entityManager->remove($offer);
            $entityManager->flush();

            return $this->redirectToRoute('offers_company_finished', ['id' =>  $companyId]);
        }
    }
}
