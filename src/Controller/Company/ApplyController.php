<?php

namespace App\Controller\Company;

use App\Entity\Offers;
use App\Entity\Student;
use App\Repository\OffersRepository;
use App\Repository\StudentRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApplyController extends AbstractController
{
    // /**
    //  * @Route("/offers/{id}/student/{student_id}", name="apply", methods={"POST"})
    //  * @ParamConverter("student", options={"id" = "student_id"})
    //  */
    // public function apply(Offers $offers, Student $student)
    // {
    //     $offers->addStudent($student);
    //     $manager = $this->getDoctrine()->getManager()->flush();
  
    //     return $this->render('offers/show.html.twig', [
    //         'controller_name' => 'ApplyController',
    //         'offers' => $offers
    //     ]);
    // }

    // /**
    //  * @Route("/studentapply/{id}", name="student_apply", methods={"GET"})
    //  */
    // public function studentApply(StudentRepository $repository, Student $student)
    // {
    //     $repository->find($student->getId());

    //     $offers = $student->getOffers();

    //     return $this->render('apply/index_student.html.twig', [
    //         'controller_name' => 'ApplyController',
    //         'offers' => $offers,
    //         'student' => $student
    //     ]);
    // }

    
    
}