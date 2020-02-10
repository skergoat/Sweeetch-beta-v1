<?php

namespace App\Controller\Company;

use App\Entity\Offers;
use App\Entity\Student;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApplyController extends AbstractController
{
    /**
     * @Route("/offers/{id}/student/{student_id}", name="apply", methods={"POST"})
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function apply(Offers $offers, Student $student)
    {
        $offers->addStudent($student);
        $student->addOffer($offers);

        $manager = $this->getDoctrine()->getManager()->flush();
  
        return $this->render('offers/show.html.twig', [
            'controller_name' => 'ApplyController',
            'offers' => $offers
        ]);
    }
}
