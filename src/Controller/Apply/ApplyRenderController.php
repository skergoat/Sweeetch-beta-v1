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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApplyRenderController extends AbstractController
{
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
        return $this->render('apply/index_company.html.twig', [
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
       
        return $this->render('apply/show_preview.html.twig', [
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
        return $this->render('apply/show_hired.html.twig', [
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
}
