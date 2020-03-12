<?php 

namespace App\Controller\University;

use DateTimeZone;
use App\Entity\School;
use App\Entity\Recruit;
use App\Entity\Student;
use App\Entity\Studies;
use App\Form\StudiesType;
use App\Repository\ApplyRepository;
use App\Repository\RecruitRepository;
use App\Repository\StudiesRepository;
use App\Service\Mailer\RecruitMailer;
use App\Service\Recruitment\ApplyHelper;
use App\Service\Recruitment\RecruitHelper;
use App\Service\UserChecker\SchoolChecker;
use App\Service\UserChecker\StudentChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/studies")
 */
class StudiesActionController extends AbstractController
{
   /**
     * @Route("/study/{id}/student/{student_id}/", name="recruit", methods={"POST"})
     * @IsGranted("ROLE_STUDENT_HIRED")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function recruit(Studies $studies, Student $student, RecruitRepository $repository, Request $request, RecruitHelper $helper, RecruitMailer $mailer)
    {
        // check if student is already hired
        if($helper->checkAgree('student', $student)) {
            $this->addFlash('error', 'Vous êtes déjà embauché ailleurs. Rendez-vous sur votre profil.');
            return  $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        }

        // check if some offers are waiting
        if($helper->checkHired('student', $student)){
            $this->addFlash('error', 'Vous avez des offres en attente. Consultez votre profil');
            return  $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        }

        // check if student is refused
        if($helper->checkRefused($studies, $student)) {  
            $this->addFlash('error', 'Offre non disponible');
            return  $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        }

        // check if student has already applied to current study
        if($helper->checkRecruit($studies, $student) && !$helper->checkFinished('student', $student)) {    
            $this->addFlash('error', 'Vous avez déjà postulé');
            return  $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        }

        if($this->isCsrfTokenValid('recruit'.$student->getId(), $request->request->get('_token'))) {
            // send notification
            $mailer->sendRecruitNotification($studies);
            // create entity
            $recruit = new Recruit; 
            $recruit->setHired(false);
            $recruit->setAgree(false);
            $recruit->setFinished(false);
            $recruit->setDateRecruit(new \DateTime('now', new DateTimeZone('Europe/Paris')));
            $recruit->setDateFinished(new \DateTime('now', new DateTimeZone('Europe/Paris')));
            $recruit->setRefused(false);
            $recruit->setUnavailable(false);
            $recruit->setStudies($studies);
            $recruit->setStudent($student);
            // save
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($recruit);
            $manager->flush();

            $this->addFlash('success', 'Candidature enregistrée !');
            return $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        }
        else {
            $this->addFlash('error', 'Requête invalide');
            return  $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        }
    }

    /**
    * @Route("/hire/{id}", name="recruit_hire", methods={"POST"})
    * @IsGranted("ROLE_SUPER_SCHOOL")
    */
    public function hire(RecruitRepository $repository, Recruit $recruit, Request $request, RecruitHelper $helper)
    {   
        // get users
        $student = $recruit->getStudent();
        $studies = $recruit->getstudies();

        // check if student is available
        if($helper->checkAgree('student', $student)) {
            $this->addFlash('error', 'Cet étudiant n\'est plus disponible.');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }
        
        if($this->isCsrfTokenValid('hire'.$recruit->getId(), $request->request->get('_token'))) {           // not usefull to delete others 
            // set state
            $helper->hire($recruit, $student, $studies);
            // save
            $entityManager = $this->getDoctrine()->getManager()->flush();   
            $this->addFlash('success', 'Elève recruté !');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }
        else {
            $this->addFlash('error', 'Requête invalide');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }
    }

    /**
    * @Route("/agree/{id}", name="recruit_agree", methods={"POST"})
    * @IsGranted("ROLE_SUPER_STUDENT")
    */
    public function agree(RecruitRepository $repository, Recruit $recruit, Request $request, RecruitHelper $helper)
    {
        // get other applies
        $student = $recruit->getStudent();
        $studies = $recruit->getStudies();

        if($this->isCsrfTokenValid('agree'.$recruit->getId(), $request->request->get('_token'))) {
            // agree
            $helper->agree($recruit, $student, $studies);
            // save
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Cursus accepté !');
            return $this->redirectToRoute('school_student_index', ['id' => $student->getId()]);
        }
        else {
            $this->addFlash('error', 'Requête invalide');
            return $this->redirectToRoute('school_student_index', ['id' => $student->getId()]);
        }   
    }

    /**
     * @Route("/finish/{id}", name="recruit_finish", methods={"POST"})
     * @IsGranted("ROLE_SUPER_SCHOOL")
     */
    public function finish(Recruit $recruit, RecruitRepository $repository, ApplyRepository $applyRepository, Request $request, RecruitHelper $helper, ApplyHelper $applyHelper)
    {
        // get entites
        $student = $recruit->getStudent();
        $studies = $recruit->getStudies();

        $apply = $applyRepository->findBy(['student' => $student, 'confirmed' => true])[0];
        $applyHelper->finish($apply, $student, $apply->getOffers());

        if($this->isCsrfTokenValid('finish'.$recruit->getId(), $request->request->get('_token'))) {
            // finish
            $helper->finish($recruit, $student, $studies);
            // save
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Inscription Terminée');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }
        else {
            $this->addFlash('success', 'Requête invalide');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }
    }

    /**
     * @Route("/refuse/{id}", name="recruit_refuse", methods={"POST"})
     * @IsGranted("ROLE_SUPER_SCHOOL")
     */
    public function refuse(RecruitRepository $repository, Recruit $recruit, Request $request, RecruitHelper $helper)
    {
        // get entities
        $student = $recruit->getStudent();
        $studies = $recruit->getStudies();

        if($recruit->getRefused() == true) {
            $this->addFlash('error', 'Vous avez déjà refusé cette candidature');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }

        if($this->isCsrfTokenValid('refuse'.$recruit->getId(), $request->request->get('_token'))) {
            // refuse
            $helper->refuse($recruit, $student, $studies);
            // save 
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Candidature refusée');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }
        else {
            $this->addFlash('error', 'Requête Invalide');
            return $this->redirectToRoute('school_studies_show', ['id' => $studies->getId(), 'school_id' => $studies->getSchool()->getId()]);
        }  
    }

    /**
     * @Route("/delete/recruit/{id}", name="delete_recruit", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_STUDENT")
     * @ParamConverter("recruit", options={"id" = "id"})
     */
    public function recruitDelete(Recruit $recruit, Request $request, RecruitRepository $repository, StudentChecker $checker, RecruitMailer $mailer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recruit->getId(), $request->request->get('_token'))) {
            // entities 
            $student = $recruit->getStudent();
            $studies = $recruit->getStudies();
            // send notification
            // $mailer->sendDeleteNotification($studies);
            // save and delete
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recruit);
            $entityManager->flush();

            $this->addFlash('success', 'Postulation supprimée !');
            return $this->redirectToRoute('school_student_index', ['id' => $student->getId()]);
        }
        else {
            $this->addFlash('error', 'Requête Invalide');
            return $this->redirectToRoute('school_student_index', ['id' => $student->getId()]);
        }
    }

     /**
     * @Route("/new/{school}", name="studies_new", methods={"GET","POST"})
     */
    public function new(Request $request, School $school, SchoolChecker $checker): Response
    {
        if ($checker->schoolValid($school)) {

            $study = new Studies();
            $form = $this->createForm(StudiesType::class, $study);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $study = $form->getData();
                $study->setSchool($school);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($study);
                $entityManager->flush();

                return $this->redirectToRoute('school_studies_index', [ 'id' => $school->getId() ]);
            }

            return $this->render('studies/new.html.twig', [
                'study' => $study,
                'form' => $form->createView(),
                'school' => $school
            ]);
        }
    }

    /**
     * @Route("/{id}/edit/{school_id}", name="studies_edit", methods={"GET","POST"})
     * @ParamConverter("school", options={"id" = "school_id"})
     */
    public function edit(Request $request, Studies $study, School $school, SchoolChecker $checker): Response
    {
        if ($checker->schoolStudiesEditValid($school, $study)) {

            $form = $this->createForm(StudiesType::class, $study);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
            }

            return $this->render('studies/edit.html.twig', [
                'study' => $study,
                'form' => $form->createView(),
                'school' => $school
            ]);
        }
    }

    /**
     * @Route("/{id}/{school_id}", name="studies_delete", methods={"DELETE"})
     * @ParamConverter("school", options={"id" = "school_id"})
     */
    public function delete(Request $request, Studies $study, School $school, RecruitHelper $helper): Response
    {
        if ($this->isCsrfTokenValid('delete'.$study->getId(), $request->request->get('_token'))) {
            // handle recruit 
            $helper->handleDeleteRecruit($study, $school);
            // delete 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($study);
            $entityManager->flush();
            return $this->redirectToRoute('school_studies_index', [ 'id' => $school->getId() ]);
        }
        else {
            $this->addFlash('error', 'Requête Invalide');
            return $this->redirectToRoute('school_studies_index', [ 'id' => $school->getId() ]);
        }   
    }
}