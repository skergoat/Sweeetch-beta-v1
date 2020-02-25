<?php

namespace App\Controller\University;

use App\Entity\School;
use App\Entity\Student;
use App\Entity\Studies;
use App\Repository\ApplyRepository;
use App\Repository\SchoolRepository;
use App\Repository\StudiesRepository;
use App\Service\UserChecker\SchoolChecker;
use App\Service\UserChecker\StudentChecker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

 /**
 * @Route("/school")
 */
class SchoolRenderController extends AbstractController
{
     /**
     * @Route("/studies/index/{id}", name="school_studies_index", methods={"GET"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function index(StudiesRepository $studiesRepository, School $school, SchoolChecker $checker): Response
    {
        if ($checker->schoolValid($school)) {

            return $this->render('studies/index.html.twig', [
                'studies' => $studiesRepository->findBy(['school' => $school]),
                'school' => $school
            ]);
        }
    }

    /**
     * @Route("/student/{id}", name="school_student_index", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexByStudent(Student $student, SchoolRepository $schoolRepository, ApplyRepository $applyRepository, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('school/index_student.html.twig', [
                'student' => $student,
                'fresh' => $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->checkIfHired($student)
            ]);
        } 
    }

    //  /**
    //  * @Route("/studies/show/{id}/{school_id}", name="school_studies_show", methods={"GET"})
    //  * @ParamConverter("school", options={"id" = "school_id"})
    //  * @IsGranted("ROLE_SCHOOL")
    //  */
    // public function show(Studies $study, School $school): Response
    // {
    //     return $this->render('studies/show.html.twig', [
    //         'study' => $study,
    //         'school' => $school
    //     ]);
    // }

}
