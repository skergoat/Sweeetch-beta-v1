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

        // if($applies->getRefused() == true) {
        //     throw new \Exception('refused');
        // }
        if($applies) {  

            $refused = $repository->checkIfrefusedExsists($offers, $student);
            
            if($refused) {
                throw new \Exception('you have been refused');
            }
            else {
                throw new \Exception('already applied');
            }  
        }

        if($applies) {
            throw new \Exception('you have been refiused');
        }

        // $company = $offers->getCompany();
        // $company->getUser()->setRoles(['ROLE_SUPER_COMPANY', 'ROLE_VISITOR']);
        // dd($company);
        
        // if($applies == false) {
        $apply = new Apply; 
        $apply->setOffers($offers);
        $apply->setStudent($student);
    
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($apply);
        $manager->flush();
        // }
        
        return $this->render('offers/show.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/studentapply/{id}", name="student_apply", methods={"GET"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     */
    public function indexByStudent(StudentRepository $repository, Student $student)
    {
        return $this->render('apply/index_student.html.twig', [
            'student' => $student
        ]);
    }

    /**
     * @Route("index/company/{id}", name="offers_company_index", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function indexByCompany(Company $company, OffersRepository $offersRepository, ApplyRepository $applyRepository): Response
    {       
        return $this->render('offers/index_company.html.twig', [
            'offers' => $offersRepository->findBy(['company' => $company->getId()]),
            'company' => $company,
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
        if($apply->getHired() == null && $apply->getConfirmed() == null) {
            $apply->setHired(true);
        }

        // get other applies
        $student = $apply->getStudent();
        $offers = $apply->getOffers();

        // prevent student from applying 
        $student->getUser()->setRoles(['ROLE_SUPER_STUDENT']);

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
            'applies' => $repository->getSingleHiredRow($offers, $student)
        ]);
    }

    /**
     * @Route("confirm/{id}", name="confirm", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function confirm(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer)
    {
        // set apply state 
        if($apply->getHired() == true && $apply->getConfirmed() == null) {
            $apply->setHired(null);
            $apply->setConfirmed(true);
        }

         // get other applies
         $student = $apply->getStudent();
         $offers = $apply->getOffers();

        $this->getDoctrine()->getManager()->flush();

        return $this->render('offers/show_preview.html.twig', [
            'offers' => $offers,
            'applies' => $repository->getSingleConfirmedRow($offers, $student)
        ]);
    }

     /**
     * @Route("/refuse/{id}", name="apply_refuse", methods={"POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function refuse(ApplyRepository $repository, Apply $apply, ApplyMailer $mailer)
    {
        $apply->setHired(null);
        $apply->setConfirmed(null);
        $apply->setRefused(true);

        // set appliant roles 
        $user = $apply->getStudent()->getUser();
        $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']);

         // get other applies
         $student = $apply->getStudent();
         $offers = $apply->getOffers();

        $this->getDoctrine()->getManager()->flush();

        return $this->render('offers/show_preview.html.twig', [
            'offers' => $offers,
            'applies' => $repository->getSingleConfirmedRow($offers, $student)
        ]);
    }

    /**
     * @Route("/delete/{id}", name="apply_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("apply", options={"id" = "id"})
     */
    public function delete(Request $request, Apply $apply, OffersRepository $offersRepository, CompanyRepository $companyRepository, ApplyMailer $mailer): Response
    {
        // get company to render company page 
        $companyId = $apply->getOffers()->getCompany()->getId();

        // set appliant roles 
        $user = $apply->getStudent()->getUser();
        $user->setRoles(['ROLE_SUPER_STUDENT', 'ROLE_TO_APPLY']);

        // send mail 
        $email = $user->getEmail();
        $name = $apply->getStudent()->getName();
        $offerTitle = $apply->getOffers()->getTitle();

        $mailer->sendDeleteMessage($email, $name, $offerTitle); 
       
        // delete apply 
        if ($this->isCsrfTokenValid('delete'.$apply->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($apply);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offers_company_index', [
            'id' => $companyId,
        ]);
    }
}
