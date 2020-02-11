<?php

namespace App\Controller\Company;

use App\Entity\Apply;
use App\Entity\Offers;
use App\Entity\Student;
use App\Repository\ApplyRepository;
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

    
    

    
    
}
