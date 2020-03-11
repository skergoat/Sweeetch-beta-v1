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
use App\Service\UserChecker\SchoolChecker;
use App\Service\UserChecker\StudentChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/studies")
 */
class StudiesRenderController extends AbstractController
{
     /**
     * @Route("/index/{id}", name="school_studies_index", methods={"GET"})
     * @IsGranted("ROLE_SUPER_SCHOOL")
     */
    public function index(StudiesRepository $studiesRepository, School $school, SchoolChecker $checker): Response
    {
        if ($checker->schoolValid($school)) {

            return $this->render('studies/index.html.twig', [
                'studies' => $studiesRepository->findBy(['school' => $school]),
                'school' => $school,
            ]);
        }
    }

    /**
     * @Route("/student/{id}", name="school_student_index", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexByStudent(Student $student, RecruitRepository $recruitRepository, SchoolRepository $schoolRepository, ApplyRepository $applyRepository, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('school/index_student.html.twig', [
                'student' => $student,
                'recruit' => $recruitRepository->findBy(['student' => $student, 'refused' => false, 'unavailable' => false, 'finished' => false], ['hired' => 'desc']),
                'fresh' => $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                'finished' => $recruitRepository->findBy(['student' => $student, 'finished' => true]),
            ]);
        } 
    }

    /**
     * @Route("/student/finished/{id}", name="school_student_finished", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexfinished(Student $student, RecruitRepository $recruitRepository, SchoolRepository $schoolRepository, ApplyRepository $applyRepository, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('school/index-finished.html.twig', [
                'student' => $student,
                'finished' => $recruitRepository->findBy(['student' => $student, 'finished' => true], ['date_finished' => 'desc']),
                'fresh' => $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true])
            ]);
        } 
    }

     /**
     * @Route("/show/{id}/{school_id}", name="school_studies_show", methods={"GET"})
     * @ParamConverter("school", options={"id" = "school_id"})
     * @IsGranted("ROLE_SUPER_SCHOOL")
     */
    public function show(Studies $study, School $school, RecruitRepository $recruitRepository): Response
    {
        return $this->render('studies/show.html.twig', [
            'study' => $study,
            'school' => $school,
            'finished' => $recruitRepository->findBy(['studies' => $study, 'finished' => true], ['date_finished' => 'desc']),
            'recruit' => $recruitRepository->findBy(['studies' => $study, 'hired' => false, 'agree' => false, 'refused' => false, 'unavailable' => false, 'finished' => false], ['date_recruit' => 'desc']),
            'process' => $recruitRepository->findProcessing($study)
        ]);
    }

    /**
     * @Route("/candidate/{from}/{id}", name="studies_candidate_index", methods={"GET"})
     * @IsGranted("ROLE_RECRUIT")
     */
    public function indexCandidate(StudiesRepository $studiesRepository, PaginatorInterface $paginator, Request $request, $from, $id): Response
    {
        $queryBuilder = $studiesRepository->findAll();

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('studies/index-student.html.twig', [
            'studies' => $pagination,
            'from' => $from,
            'id' => $id,
        ]);
    } 

    /**
    * @Route("/show/recruit/{id}/{from}/{from_id}", name="studies_show_recruit", methods={"GET"})
    * @IsGranted("ROLE_RECRUIT")
    */
    public function showRecruit(Studies $study, $from, $from_id) 
    {
        return $this->render('studies/show-recruit.html.twig', [
            'study' => $study,
            'from' => $from,
            'from_id' => $from_id
        ]);
    }

    /**
    * @Route("/show/hired/{id}/student/{student}", name="show_student_hired", methods={"GET"})
    * @IsGranted("ROLE_STUDENT_HIRED")
    * @ParamConverter("student", options={"id" = "student"})
    */
    public function showHired(Studies $studies, Student $student, ApplyRepository $applyRepository)
    {
        // si autorise que pour son id et celle de ses ecoles ca devrait etre ok !!!!
        // attention a ce qu'il voit pas les unavailables pendant le recrutement !!!!
        return $this->render('studies/show_hired.html.twig', [
            'studies' => $studies,
            'student' => $student,
            'fresh' => $applyRepository->findByStudentByFresh($student),
            'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true])
        ]);  
    }

    /**
    * @Route("/show/applied/{id}/{school}/{study}", name="show_student_applied", methods={"GET"})
    * @IsGranted("ROLE_SUPER_SCHOOL")
    * @ParamConverter("school", options={"id" = "school"})
    * @ParamConverter("study", options={"id" = "study"})
    */
    public function showApplied(Student $student, School $school, Studies $study)
    {
        return $this->render('studies/show-applied.html.twig', [
            'student' => $student,
            'school' => $school,
            'study' => $study
        ]);
    } 
}
