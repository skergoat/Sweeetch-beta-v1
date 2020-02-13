<?php

namespace App\Controller\Company;

use App\Entity\Apply;
use App\Entity\Offers;
use App\Entity\Company;
use App\Entity\Student;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use App\Repository\OffersRepository;
use App\Repository\CompanyRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApplyController extends AbstractController
{
    /**
     * @Route("/offers/{id}/student/{student_id}", name="apply", methods={"POST"})
     * @IsGranted("ROLE_TO_APPLY")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function apply(ApplyRepository $repository, Offers $offers, Student $student)
    {
        $applies = $repository->checkIfRowExsists($offers, $student);

        if($applies) {  

            $refused = $repository->checkIfrefusedExsists($offers, $student);
            
            if($refused) {
                throw new \Exception('already applied or refused');
            }
            else {
                throw new \Exception('already applied');
            }  
        }

        if($applies) {
            throw new \Exception('you have been refiused');
        }

        $apply = new Apply; 
        $apply->setHired(false);
        $apply->setConfirmed(false);
        $apply->setRefused(false);
        $apply->setUnavailable(false);
        $apply->setFinished(false);
        $apply->setOffers($offers);
        $apply->setStudent($student);
    
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($apply);
        $manager->flush();
 
        
        return $this->render('offers/show.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/studentapply/{id}", name="student_apply", methods={"GET"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     */
    public function indexByStudent(StudentRepository $repository, applyRepository $applyRepository, Student $student)
    {   
        $applies = $applyRepository->findByStudent($student);
        $finished = $applyRepository->findByStudentByFinished($student);

        return $this->render('apply/index_student.html.twig', [
            'student' => $student,
            'applies' => $applies,
            'finished' => $finished
        ]);
    }

    /**
     * @Route("index/company/{id}", name="offers_company_index", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function indexByCompany(Company $company, OffersRepository $offersRepository): Response
    {       
        return $this->render('offers/index_company.html.twig', [
            'offers' => $offersRepository->findBy(['company' => $company->getId()]),
            'company' => $company,
        ]);
    }

    /**
     * @Route("/preview/{id}", name="offers_preview", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function showPreview(ApplyRepository $applyRepository, Offers $offer): Response
    {   
        $applies = $applyRepository->findByOffer($offer);
        $finished = $applyRepository->findByOfferByFinished($offer);
       
        return $this->render('offers/show_preview.html.twig', [
            'offers' => $offer,
            'applies' => $applies,
            'finished' => $finished
        ]);
    }

    /**
     * @Route("/hired/{id}/student/{student_id}", name="offers_show_hired", methods={"GET"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function showHired(StudentRepository $studentRepository, Offers $offer, Student $student): Response
    {   
        return $this->render('offers/show_hired.html.twig', [
            'offers' => $offer,
            'student' => $student
        ]);
    }

     /**
     * @Route("/showapplied/{id}/company/{company_id}", name="show_applied_profile", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company_id"})
     */
    public function showAppliedProfile(Student $student, Company $company): Response
    {   
        return $this->render('apply/show_applied.html.twig', [
            'student' => $student,
            'company' => $company
        ]);
    }

    /**
     * @Route("hire/{id}", name="hire", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function hire(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer)
    {   
        // set apply state 
        if($apply->getHired() == false && $apply->getConfirmed() == false) {
            $apply->setHired(true);
            $apply->setConfirmed(false);
            $apply->setRefused(false);
        }

        // get other applies
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        // prevent student from applying 
        $student->getUser()->setRoles(['ROLE_SUPER_STUDENT']);

        // set other student offers to unavailable
        $unavailables = $repository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getRefused() != true && $unavailables->getFinished() != true) {
                $unavailables->setUnavailable(true);
            }
            
        }

        $entityManager = $this->getDoctrine()->getManager();
 
        $others = $repository->getOtherApplies($student->getId(), $offers->getId());

        if($others) {

            foreach($others as $others) {

                // send mail to other applies 
                $offerTitle = $others->getOffers()->getTitle();
                $name = $others->getStudent()->getName();
                $email = $others->getStudent()->getUser()->getEmail();
        
                $mailer->sendOthersMessage($email, $name, $offerTitle); 

                // delete other applies 
                $entityManager->remove($others);   
            }
        }

        // save 
        $entityManager->flush();

        return $this->render('offers/show_preview.html.twig', [
            'offers' => $offers,
            'applies' => $repository->getSingleHiredRow($offers, $student),
            'finished' =>  $repository->findByOfferByFinished($offers)
        ]);
    }

    /**
     * @Route("confirm/{id}", name="confirm", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function confirm(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer)
    {
        // set apply state 
        if(    $apply->getHired() == true 
            && $apply->getConfirmed() == false 
            && $apply->getRefused() == false 
        ) {
            $apply->setHired(false);
            $apply->setConfirmed(true);
            $apply->setRefused(false);
        }

         // get other applies
         $student = $apply->getStudent();
         $offers = $apply->getOffers();

         $student->getUser()->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_HIRED']);

        $this->getDoctrine()->getManager()->flush();

        return $this->render('offers/show_preview.html.twig', [
            'offers' => $offers,
            'applies' => $repository->getSingleConfirmedRow($offers, $student),
            'finished' =>  $repository->findByOfferByFinished($offers)
        ]);
    }

    //  /**
    //  * @Route("/quit/{id}", name="apply_quit", methods={"POST"})
    //  * @IsGranted("ROLE_SUPER_STUDENT")
    //  */
    // public function quit(ApplyRepository $repository, Apply $apply) 
    // {
    //     // $apply->setHired(false);
    //     // $apply->setConfirmed(false);
        
    //     // set appliant roles 
    //     $user = $apply->getStudent()->getUser();
    //     $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']);

    //      // get other applies
    //      $student = $apply->getStudent();
    //      $offers = $apply->getOffers();

    //     // set other student offers to available
    //     $unavailables = $repository->setToUnavailables($offers, $student);

    //     foreach($unavailables as $unavailables) {

    //         if($unavailables->getUnavailable() == true) {
    //             $unavailables->setUnavailable(false);
    //         }      
    //     }

    //     $manager = $this->getDoctrine()->getManager();
    //     $manager->remove($apply);
    //     $manager->flush();

    //     return $this->redirectToRoute('student_apply', ['id' => $student->getId()]);

    // }

     /**
     * @Route("/refuse/{id}/{from}", name="apply_refuse", methods={"POST"})
     * @IsGranted("ROLE_RELATION")
     */
    public function refuse(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer, $from)
    {
        if($apply->getRefused() == true) {
            throw new \Exception('already refused');
        }

        $apply->setHired(false);
        $apply->setConfirmed(false);
        $apply->setRefused(true);

        // set appliant roles 
        $user = $apply->getStudent()->getUser();
        $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']);

        // set com,pany roles - security 
        $company = $apply->getOffers()->getCompany()->getUser();
        $roles = $company->getRoles();

        if(in_array('ROLE_IS_REFUSED', $roles)) {
            $company->setRoles(['ROLE_SUPER_COMPANY']);
        }
        else {
            $company->setRoles(['ROLE_SUPER_COMPANY', 'ROLE_IS_REFUSED']);
        }

         // get other applies
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        // set other student offers to available
        $unavailables = $repository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getUnavailable() == true) {
                $unavailables->setUnavailable(false);
            }      
        }

        $this->getDoctrine()->getManager()->flush();

        if($from == 'student') {
            $return = $this->redirectToRoute('student_apply', ['id' => $student->getId()]);
        }
        else if($from == 'company') {
            $return = $this->redirectToRoute('offers_company_index', ['id' => $apply->getOffers()->getCompany()->getId()]);
        }

        return $return;
    }

     /**
     * @Route("/finish/{id}", name="apply_finish", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function finish(Apply $apply, ApplyRepository $applyRepository)
    {
        $apply->setConfirmed(false);
        $apply->setFinished(true);

        // set appliant roles 
        $user = $apply->getStudent()->getUser();
        $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']); 

        // get other applies
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        // set other student offers to available
        $unavailables = $applyRepository->setToUnavailables($offers, $student);

        foreach($unavailables as $unavailables) {

            if($unavailables->getUnavailable() == true) {
                $unavailables->setUnavailable(false);
            }      
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('offers_company_index', ['id' => $apply->getOffers()->getCompany()->getId()]);
    }

    // /**
    //  * @Route("/delete/{id}", name="apply_delete", methods={"DELETE"})
    //  * @IsGranted("ROLE_SUPER_COMPANY")
    //  * @ParamConverter("apply", options={"id" = "id"})
    //  */
    // public function delete(Request $request, Apply $apply, ApplyRepository $repository, OffersRepository $offersRepository, CompanyRepository $companyRepository, ApplyMailer $mailer): Response
    // {
    //     // get company to render company page 
    //     $companyId = $apply->getOffers()->getCompany()->getId();

    //     // set appliant roles 
    //     $user = $apply->getStudent()->getUser();
    //     $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']);

    //     $student = $apply->getStudent();
    //     $offers = $apply->getOffers();

    //     // set other student offers to unavailable
    //     $unavailables = $repository->setToUnavailables($offers, $student);

    //     foreach($unavailables as $unavailables) {

    //         if($unavailables->getUnavailable() == true) {
    //             $unavailables->setUnavailable(false);
    //         } 
    //     }

    //     // send mail 
    //     $email = $user->getEmail();
    //     $name = $apply->getStudent()->getName();
    //     $offerTitle = $apply->getOffers()->getTitle();

    //     $mailer->sendDeleteMessage($email, $name, $offerTitle); 
       
    //     // delete apply 
    //     if ($this->isCsrfTokenValid('delete'.$apply->getId(), $request->request->get('_token'))) {

    //         $entityManager = $this->getDoctrine()->getManager();
    //         // delete relation
    //         $entityManager->remove($apply);
    //         // delete offer
    //         $entityManager->remove($apply->getOffers());
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('offers_company_index', ['id' => $companyId,]);
    // }
}
