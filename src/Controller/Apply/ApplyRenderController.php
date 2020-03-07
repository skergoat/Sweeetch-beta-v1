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
use App\Service\UserChecker\CompanyChecker;
use App\Service\UserChecker\StudentChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/apply")
 */
class ApplyRenderController extends AbstractController
{
     /**
     * @Route("/index/student/{id}", name="student_apply", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexByStudent(StudentRepository $repository, applyRepository $applyRepository, Student $student, StudentChecker $checker)
    {   
        if ($checker->studentValid($student)) {    
            return $this->render('apply/index_student.html.twig', [
                'student' => $student,
                'applies' => $applyRepository->findBy(['student' => $student, 'refused' => false, 'unavailable' => false], ['hired' => 'desc']),
                'finished' => $applyRepository->findBy(['student' => $student, 'finished' => true]),
                'fresh' =>  $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->checkIfHired($student)
            ]);
        }
    }

    /**
     * @Route("/finished/student/{id}", name="student_finished", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function finishedByStudent(StudentRepository $repository, applyRepository $applyRepository, Student $student, StudentChecker $checker)
    {   
        if ($checker->studentValid($student)) {
            $applies = $applyRepository->findByStudent($student);
            $finished = $applyRepository->findByStudentByFinished($student);
        
            return $this->render('apply/finished-student.html.twig', [
                'student' => $student,
                'applies' => $applies,
                'finished' => $finished,
                'fresh' =>  $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->checkIfHired($student)
            ]);
        }
    }

    /**
     * @Route("/index/company/{id}", name="offers_company_index", methods={"GET"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function indexByCompany(Company $company, OffersRepository $offersRepository, ApplyRepository $applyRepository, PaginatorInterface $paginator, Request $request, CompanyChecker $checker): Response
    {     
        if($checker->companyValid($company)) {

            $queryBuilder = $offersRepository->findAllPaginatedByCompany("DESC", $company);
            
            $pagination = $paginator->paginate(
                $queryBuilder,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('apply/index_company.html.twig', [
                'offers' => $pagination,
                'company' => $company,
                'hired' => $applyRepository->findBy(['offers' => $queryBuilder, 'hired' => 1]),
                'agree' => $applyRepository->findBy(['offers' => $queryBuilder, 'agree' => 1]),
                'applies' => $applyRepository->findBy(['offers' => $queryBuilder, 'finished' => 1]),
                'applyc' => $applyRepository->findBy(['offers' => $queryBuilder, 'refused' => 0, 'unavailable' => 0, 'confirmed' => 0, 'finished' => 0])
            ]);
        }
    }

     /**
     * @Route("/finished/company/{id}", name="offers_company_finished", methods={"GET"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function finishedByCompany(Company $company, OffersRepository $offersRepository, ApplyRepository $applyRepository, PaginatorInterface $paginator, Request $request, CompanyChecker $checker): Response
    {     
        if($checker->companyValid($company)) {

            $queryBuilder = $offersRepository->findAllPaginatedByCompany("DESC", $company);
            $test = $applyRepository->findBy(['offers' => $queryBuilder, 'finished' => 1]);

            $pagination = $paginator->paginate(
                $test,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('apply/finished_company.html.twig', [
                'offers' => $queryBuilder,
                'company' => $company,
                'applies' => $pagination,
                'hired' => $applyRepository->findBy(['offers' => $queryBuilder, 'hired' => 1]),
                'agree' => $applyRepository->findBy(['offers' => $queryBuilder, 'agree' => 1]),
                'applyc' => $applyRepository->findBy(['offers' => $queryBuilder, 'refused' => 0, 'unavailable' => 0, 'confirmed' => 0, 'finished' => 0])

            ]);
        }
    }

    /**
     * @Route("/show/company/{id}/{company}", name="offers_preview", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company"})
     */
    public function showByCompany(ApplyRepository $applyRepository, Offers $offer, Company $company, OffersRepository $offersRepository, CompanyChecker $checker): Response
    {   
        if($checker->companyOffersValid($company, $offer)) {

            $applies = $applyRepository->findByOffer($offer);
            $finished = $applyRepository->findByOfferByFinished($offer);

            $offers = $offersRepository->findBy(['company' => $company]);
        
            return $this->render('apply/show_preview.html.twig', [
                'offers' => $offer,
                'applies' => $applies,
                'finished' => $finished,
                'company' => $company,
                'hired' => $applyRepository->findBy(['offers' => $offers, 'hired' => 1]),
                'agree' => $applyRepository->findBy(['offers' => $offers, 'agree' => 1]),
                'appliesf' => $applyRepository->findBy(['offers' => $offers, 'finished' => 1]),
                'applyc' => $applyRepository->findBy(['offers' => $offers, 'refused' => 0, 'unavailable' => 0, 'confirmed' => 0, 'finished' => 0])
            ]);
        }
    }

    /**
     * @Route("/profile/{id}/student/{student_id}", name="offers_show_hired", methods={"GET"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function showOfferProfile(StudentRepository $studentRepository, ApplyRepository $applyRepository, Offers $offer, Student $student, StudentChecker $checker): Response
    {   
       if($checker->studentApplyValid($student, $offer)) {
            return $this->render('apply/show_hired.html.twig', [
                'offers' => $offer,
                'student' => $student,
                'fresh' =>  $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->checkIfHired($student),
                'finished' => $applyRepository->findByStudentByFinished($student)
            ]);
        }
    }

     /**
     * @Route("/profile/{id}/company/{company_id}/offers/{offers}", name="show_applied_profile", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company_id"})
     * @ParamConverter("offers", options={"id" = "offers"})
     */
    public function showStudentProfile(Student $student, Company $company, Offers $offers, ApplyRepository $applyRepository, AuthorizationCheckerInterface $authorizationChecker, OffersRepository $offersRepository, CompanyChecker $checker): Response
    {   
        if($checker->studentProfileValid($company, $offers, $student)) {

            $offer = $offersRepository->findBy(['company' => $company]);

            return $this->render('apply/show_applied.html.twig', [
                'student' => $student,
                'company' => $company,
                'offers' => $offers,
                'hired' => $applyRepository->findBy(['offers' => $offer, 'hired' => 1]),
                'agree' => $applyRepository->findBy(['offers' => $offer, 'agree' => 1]),
                'applies' => $applyRepository->findBy(['offers' => $offer, 'finished' => 1]),
                'applyc' => $applyRepository->findBy(['offers' => $offer, 'refused' => 0, 'unavailable' => 0, 'confirmed' => 0, 'finished' => 0])
            ]);
        }
    }
}
