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
        $company = $offers->getCompany();
        $company->getUser()->setRoles(['ROLE_SUPER_COMPANY', 'ROLE_VISITOR']);
        // dd($company);
        $applies = $repository->checkIfRowExsists($offers, $student);
        
        if($applies == false) {

            $apply = new Apply; 
            $apply->setOffers($offers);
            $apply->setStudent($student);
        
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($apply);
            $manager->flush();
        }
       
        return $this->render('offers/show.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/studentapply/{id}", name="student_apply", methods={"GET"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     */
    public function studentApplies(StudentRepository $repository, Student $student)
    {
        return $this->render('apply/index_student.html.twig', [
            'student' => $student
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
     * @Route("confirm/{id}/offers_id", name="confirm", methods={"POST"})
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
     * @Route("/{id}", name="apply_delete", methods={"DELETE"})
     * @ParamConverter("apply", options={"id" = "id"})
     */
    public function delete(Request $request, Apply $apply, OffersRepository $offersRepository, CompanyRepository $companyRepository): Response
    {
        $companyId = $apply->getOffers()->getCompany()->getId();

        // dd();
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
