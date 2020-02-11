<?php

namespace App\Controller\Company;

use App\Entity\Apply;
use App\Entity\Offers;
use App\Entity\Student;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use App\Repository\OffersRepository;
use App\Repository\StudentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApplyController extends AbstractController
{
    /**
     * @Route("/offers/{id}/student/{student_id}", name="apply", methods={"POST"})
     * @IsGranted("ROLE_SUPER_STUDENT")
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
            'controller_name' => 'ApplyController',
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
        if($apply->getHired() == false && $apply->getConfirmed() == false) {
            $apply->setHired(true);
        }

        // get other applies
        $student = $apply->getStudent()->getId();
        $offers = $apply->getOffers()->getId();
        
        $others = $repository->getOtherApplies($student, $offers);

        foreach($others as $others) {

            // send mail to other applies 
            $offerTitle = $others->getOffers()->getTitle();
            $name = $others->getStudent()->getName();
            $email = $others->getStudent()->getUser()->getEmail();
    
            $mailer->sendOthersMessage($email, $name, $offerTitle); 

            // delete other applies 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($others);
            $entityManager->flush();
        }

        dd($offerTitle);


        

        

       

        // send notifications to others 
        // delete others applies 
       
        return $this->render('offers/show.html.twig', [
            'controller_name' => 'ApplyController',
            'offers' => $offers
        ]);
    }

    
    

    
    
}
