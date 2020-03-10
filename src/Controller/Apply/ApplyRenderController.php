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
                'applies' => $applyRepository->findBy(['student' => $student, 'refused' => false, 'unavailable' => false, 'finished' => false], ['hired' => 'desc']),
                'finished' => $applyRepository->findBy(['student' => $student, 'finished' => true]),
                'fresh' =>  $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
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
            return $this->render('apply/finished-student.html.twig', [
                'student' => $student,
                'applies' => $applyRepository->findBy(['student' => $student, 'refused' => false, 'unavailable' => false, 'finished' => false]),
                'finished' => $applyRepository->findBy(['student' => $student, 'finished' => true]),
                'fresh' =>  $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true])
            ]);
        }
    }

    /**
     * @Route("/index/company/{id}", name="offers_company_index", methods={"GET"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function indexByCompany(Company $company, OffersRepository $offersRepository, ApplyRepository $applyRepository, Request $request, CompanyChecker $checker, ApplyHelper $helper): Response
    {     
        if($checker->companyValid($company)) {

            $offers = $offersRepository->findBy(['company' => $company], ['id' => 'desc']);
        
            return $this->render('apply/index_company.html.twig', [
                'offers' => $offers,
                'company' => $company,
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                'closed' =>  $helper->checkFinished('offers', $offers),
                'candidates' => $helper->nbCandidates($offers),
            ]);
        }
    }

     /**
     * @Route("/finished/company/{id}", name="offers_company_finished", methods={"GET"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function finishedByCompany(Company $company, OffersRepository $offersRepository, ApplyRepository $applyRepository, PaginatorInterface $paginator, Request $request, CompanyChecker $checker, ApplyHelper $helper): Response
    {     
        if($checker->companyValid($company)) {

            $offers = $offersRepository->findBy(['company' => $company], ['id' => 'desc']);
        
            return $this->render('apply/finished_company.html.twig', [
                'offers' => $offers,
                'company' => $company,
                'applies' => $applyRepository->findByOffersFinished($offers),
                // infos 
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                'closed' =>  $helper->checkFinished('offers', $offers),
                'candidates' => $helper->nbCandidates($offers),
            ]);
        }
    }

    /**
     * @Route("/show/company/{id}/{company}", name="offers_preview", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company"})
     */
    public function showByCompany(ApplyRepository $applyRepository, Offers $offer, Company $company, OffersRepository $offersRepository, CompanyChecker $checker, ApplyHelper $helper): Response
    {   
        if($checker->companyOffersValid($company, $offer)) {
            // get all company offers
            $offers = $offersRepository->findBy(['company' => $company]);
        
            return $this->render('apply/show_preview.html.twig', [
                'offers' => $offer, // current single offer content 
                'company' => $company, // company layout 
                'applies' => $applyRepository->findBy(['offers' => $offer, 'refused' => false, 'unavailable' => false, 'confirmed' => false, 'finished' => false]),
                 // infos
                 'hired' => $helper->checkHired('offers', $offers),
                 'agree' => $helper->checkAgree('offers', $offers),
                 'closed' =>  $helper->checkFinished('offers', $offers),
                 'candidates' => $helper->nbCandidates($offers),
            ]);
        }
    }

    /**
     * @Route("/show/finished/{id}/{company}", name="show_finished", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company"})
     */
    public function showFinished(ApplyRepository $applyRepository, Offers $offer, Company $company, OffersRepository $offersRepository, CompanyChecker $checker, ApplyHelper $helper): Response
    {   
        if($checker->companyOffersValid($company, $offer)) {
            // get all company offers
            $offers = $offersRepository->findBy(['company' => $company]);
        
            return $this->render('apply/show_finished.html.twig', [
                'offers' => $offer, // current single offer content 
                'company' => $company, // company layout 
                'finished' => $applyRepository->findByOffersByFinished($offer), // get finished
                // infos
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                'closed' =>  $helper->checkFinished('offers', $offers),
                'candidates' => $helper->nbCandidates($offers),
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
                // 'hired' => $applyRepository->checkIfHired($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                'finished' =>  $applyRepository->findBy(['student' => $student, 'finished' => true]),
            ]);
        }
    }

     /**
     * @Route("/profile/{id}/company/{company_id}/offers/{offers}", name="show_applied_profile", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company_id"})
     * @ParamConverter("offers", options={"id" = "offers"})
     */
    public function showAppliedProfile(Student $student, Company $company, Offers $offers, ApplyRepository $applyRepository, AuthorizationCheckerInterface $authorizationChecker, OffersRepository $offersRepository, CompanyChecker $checker, ApplyHelper $helper): Response
    {   
        if($checker->studentProfileValid($company, $offers, $student)) {

            $offer = $offersRepository->findBy(['company' => $company]);

            return $this->render('apply/show_applied.html.twig', [
                'student' => $student,
                'company' => $company,
                'offers' => $offers,
                 // infos 
                 'hired' => $helper->checkHired('offers', $offer),
                 'agree' => $helper->checkAgree('offers', $offer),
                //  'confirmed' => $helper->checkConfirmed('offers', $offers),
                 'finished' =>  $helper->checkFinished('offers', $offer),
                 'candidates' => $helper->nbCandidates($offer),
            ]);
        }
    }

     /**
     * @Route("/applied/{id}/company/{company_id}/offers/{offers}", name="show_applied_finished", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company_id"})
     * @ParamConverter("offers", options={"id" = "offers"})
     */
    public function showAppliedFinished(Student $student, Company $company, Offers $offers, ApplyRepository $applyRepository, AuthorizationCheckerInterface $authorizationChecker, OffersRepository $offersRepository, CompanyChecker $checker, ApplyHelper $helper): Response
    {   
        if($checker->studentProfileValid($company, $offers, $student)) {

            $offer = $offersRepository->findBy(['company' => $company]);

            return $this->render('apply/show_applied_finished.html.twig', [
                'student' => $student,
                'company' => $company,
                'offers' => $offers,
                 // infos 
                 'hired' => $helper->checkHired('offers', $offer),
                 'agree' => $helper->checkAgree('offers', $offer),
                //  'confirmed' => $helper->checkConfirmed('offers', $offers),
                 'finished' =>  $helper->checkFinished('offers', $offer),
                 'candidates' => $helper->nbCandidates($offer),
            ]);
        }
    }
}
