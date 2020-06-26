<?php

namespace App\Controller\University;

use App\Entity\School;
use App\Entity\Student;
use App\Entity\Studies;
use App\Form\StudiesType;
use App\Repository\ApplyRepository;
use App\Repository\SchoolRepository;
use App\Repository\RecruitRepository;
use App\Repository\StudiesRepository;
use App\Service\Recruitment\RecruitHelper;
use App\Service\UserChecker\SchoolChecker;
use App\Service\UserChecker\StudentChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StudiesRenderController extends AbstractController
{
     /**
     * @Route("school/studies/{id}", name="school_studies_index", methods={"GET"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function index(StudiesRepository $studiesRepository, School $school, SchoolChecker $checker, RecruitRepository $recruitRepository, RecruitHelper $recruitHelper): Response
    {        
        if ($checker->schoolValid($school)) {

            $studies = $studiesRepository->findBy(['school' => $school]);

            return $this->render('studies/index.html.twig', [
                'studies' => $studiesRepository->findBy(['school' => $school]),
                'school' => $school,
                'hired' => $recruitRepository->findBy(['studies' => $studies, 'hired' => true],['date_recruit' => 'desc']),
                'agree' => $recruitRepository->findBy(['studies' => $studies, 'agree' => true],['date_recruit' => 'desc']), 
                'candidates' => $recruitHelper->nbCandidates($studies), // show nb applies 
            ]);
        }
    }

    /**
     * @Route("/student/studies/{id}", name="school_student_index", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexByStudent(Student $student, RecruitRepository $recruitRepository, SchoolRepository $schoolRepository, ApplyRepository $applyRepository, RecruitHelper $recruitHelper, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('school/index_student.html.twig', [
                'student' => $student,
                'recruit' => $recruitRepository->findBy(['student' => $student, 'refused' => false, 'unavailable' => false, 'finished' => false], ['hired' => 'desc']),
                'fresh' => $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                'finished' => $recruitRepository->findBy(['student' => $student, 'finished' => true]),
                'freshRecruit' => $recruitRepository->findByStudentByFresh($student), // nb candidates
                'hiredRecruit' => $recruitHelper->checkHired('student', $student), // confirm warning
            ]);
        } 
    }

    /**
     * @Route("/student/finished/{id}", name="school_student_finished", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexfinished(Student $student, StudiesRepository $studiesRepository, RecruitRepository $recruitRepository, SchoolRepository $schoolRepository, ApplyRepository $applyRepository, RecruitHelper $recruitHelper, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('school/index-finished.html.twig', [
                'student' => $student,
                'finished' => $recruitRepository->findBy(['student' => $student, 'finished' => true], ['date_finished' => 'desc']),
                'fresh' => $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                'freshRecruit' => $recruitRepository->findByStudentByFresh($student), // nb candidates
                'hiredRecruit' => $recruitHelper->checkHired('student', $student), // confirm warning
                'hired' => $recruitRepository->findBy(['studies' => $student, 'hired' => true],['date_recruit' => 'desc']),
                'agree' => $recruitRepository->findBy(['studies' => $student, 'agree' => true],['date_recruit' => 'desc']), 
                'candidates' => $recruitHelper->nbCandidates($student), // show nb applies 
            ]);
        } 
    }

    //  /**
    //  * @Route("school/study/{id}/{school_id}", name="school_studies_show", methods={"GET"})
    //  * @ParamConverter("school", options={"id" = "school_id"})
    //  * @IsGranted("ROLE_SUPER_SCHOOL")
    //  */
    // public function show(Studies $study, School $school, StudiesRepository $studiesRepository, RecruitRepository $recruitRepository, RecruitHelper $recruitHelper, SchoolChecker $checker): Response
    // {
    //     if ($checker->schoolStudiesEditValid($school, $study)) {

    //         $studies = $studiesRepository->findBy(['school' => $school]);

    //         return $this->render('studies/show.html.twig', [
    //             'study' => $study,
    //             'school' => $school,
    //             'finished' => $recruitRepository->findBy(['studies' => $study, 'finished' => true], ['date_finished' => 'desc']),
    //             'recruit' => $recruitRepository->findBy(['studies' => $study, 'hired' => false, 'agree' => false, 'refused' => false, 'unavailable' => false, 'finished' => false], ['date_recruit' => 'desc']),
    //             'process' => $recruitRepository->findProcessing($study),
    //             'hired' => $recruitRepository->findBy(['studies' => $studies, 'hired' => true],['date_recruit' => 'desc']),
    //             'agree' => $recruitRepository->findBy(['studies' => $studies, 'agree' => true],['date_recruit' => 'desc']), 
    //             'candidates' => $recruitHelper->nbCandidates($studies), // show nb applies 
    //         ]);
    //     }
    // }

    /**
    * @Route("/cursus/{page<\d+>?1}", name="studies_candidate_index", methods={"GET"})
    */
    public function indexCandidate(StudiesRepository $studiesRepository, PaginatorInterface $paginator, Request $request, $page): Response
    { 
        $queryBuilder = $studiesRepository->findBy([], ['id' => 'DESC']);

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', $page)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('studies/index-cursus.html.twig', [
            'studies' => $pagination,
            'pages' => $page
        ]);
    } 

    /**
    * @Route("crusus/show/{id}/{page}", name="studies_show_recruit", methods={"GET"})
    */
    public function showRecruit(Studies $study, $page) 
    //, $from_id
    {
        return $this->render('studies/show-recruit.html.twig', [
            'study' => $study,
            'pages' => $page
            // 'from' => $from,
            // 'from_id' => $from_id
        ]);
    }

    /**
    * @Route("/show/hired/{id}/student/{student}", name="show_student_hired", methods={"GET"})
    * @IsGranted("ROLE_SUPER_STUDENT")
    * @ParamConverter("student", options={"id" = "student"})
    */
    public function showHired(Studies $studies, Student $student, ApplyRepository $applyRepository, RecruitRepository $recruitRepository, RecruitHelper $recruitHelper, SchoolChecker $checker)
    {
        // si autorise que pour son id et celle de ses ecoles ca devrait etre ok !!!!
        // attention a ce qu'il voit pas les unavailables pendant le recrutement !!!!

        if ($checker->schoolshowHireddValid($student, $studies)) {

            if($recruitHelper->checkUnavailable($studies, $student) == false) {

                if($recruitHelper->checkRefused($studies, $student) == false) {

                    return $this->render('studies/show_hired.html.twig', [
                        'studies' => $studies,
                        'student' => $student,
                        'fresh' => $applyRepository->findByStudentByFresh($student),
                        'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                        'finished' => $recruitRepository->findBy(['student' => $student, 'finished' => true]),
                        'freshRecruit' => $recruitRepository->findByStudentByFresh($student), // nb candidates
                        'hiredRecruit' => $recruitHelper->checkHired('student', $student), // confirm warning
                    ]); 
                }
                else {
                    $this->addFlash('error', 'Requête Invalide');
                    return $this->redirectToRoute('school_student_index', ['id' => $student->getId()]);
                }
            }
            else {
                $this->addFlash('error', 'Requête Invalide');
                return $this->redirectToRoute('school_student_index', ['id' => $student->getId()]);
            }
        } 
    }

    /**
    * @Route("school/profile/{id}/{school}/{study}", name="show_student_applied", methods={"GET"})
    * @IsGranted("ROLE_SUPER_SCHOOL")
    * @ParamConverter("school", options={"id" = "school"})
    * @ParamConverter("study", options={"id" = "study"})
    */
    public function showApplied(Student $student, School $school, Studies $study, SchoolChecker $checkerRecruitRepository, StudiesRepository $studiesRepository,RecruitRepository $recruitRepository, RecruitHelper $recruitHelper, SchoolChecker $checker)
    {
        if ($checker->schoolshowAppliedValid($student, $school, $study)) {

            if($recruitHelper->checkUnavailable($study, $student) == false) {

                if($recruitHelper->checkRefused($study, $student) == false) {

                    $studies = $studiesRepository->findBy(['school' => $school]);

                    return $this->render('studies/show-applied.html.twig', [
                        'student' => $student,
                        'school' => $school,
                        'study' => $study,
                        'hired' => $recruitRepository->findBy(['studies' => $studies, 'hired' => true],['date_recruit' => 'desc']),
                        'agree' => $recruitRepository->findBy(['studies' => $studies, 'agree' => true],['date_recruit' => 'desc']), 
                        'candidates' => $recruitHelper->nbCandidates($studies), // show nb applies 
                    ]);
                }
                else {
                    $this->addFlash('error', 'Requête Invalide');
                    return $this->redirectToRoute('studies_edit', ['id' => $study->getId(), 'school_id' => $school->getId()]);
                }
            }
            else {
                $this->addFlash('error', 'Requête Invalide');
                return $this->redirectToRoute('studies_edit', ['id' => $study->getId(), 'school_id' => $school->getId()]);
            }
        }
    } 
}
