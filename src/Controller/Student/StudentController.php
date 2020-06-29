<?php

namespace App\Controller\Student;

use App\Entity\User;
use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\Profile;
use App\Entity\Student;
use App\Entity\Pictures;
use App\Form\StudentType;
use App\Entity\StudentCard;
use App\Service\StudentHelper;
// use App\Entity\ProofHabitation;
use App\Form\UpdateStudentType;
use App\Service\SecurityHelper;
use App\Service\UploaderHelper;
use Gedmo\Sluggable\Util\Urlizer;
use App\Form\UpdateStudentDocType;
use App\Form\UserEditPasswordType;
use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
use App\Repository\ApplyRepository;
use App\Repository\ResumeRepository;
use App\Form\StudentEditPasswordType;
use App\Repository\RecruitRepository;
use App\Repository\StudentRepository;
use App\Form\UpdateStudentGeneralType;
use App\Service\Recruitment\ApplyHelper;
use App\Service\UserChecker\AdminChecker;
use App\Service\Recruitment\RecruitHelper;
use App\Service\UserChecker\StudentChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    
    private $entities = ['Resume', 'IdCard', 'StudentCard'];

    /**
     * @Route("/load/{data}", name="load", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
     public function load($data):Response
     {
        return $this->render('doc/doc-'.$data.'.html.twig');
     }

      /**
     * @Route("/stop/{id}", name="stop", methods={"POST"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function stop(Request $request, Student $student):Response
    {
        $student->getProfile()->setPresentation(true);
        $manager = $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('student_show', ['id' => $student->getId()]);
    }

    /**
     * @Route("/", name="student_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(StudentRepository $studentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $studentRepository->findAllPaginated("DESC");

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('student/index.html.twig', [
            'students' => $pagination,
        ]);
    }

    /**
     * @Route("/new/", name="student_new", methods={"GET","POST"})
     */
    public function new(Request $request, StudentHelper $studentHelper, SecurityHelper $securityHelper): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get student 
            $student = $form->getData();
            // upload files 
            $keys = array_keys($request->files->get('student'));
            $studentHelper->uploadNew($form, $keys, $student);
            // set user
            $securityHelper->newUser($student, 'ROLE_STUDENT');
            // create empty profile 
            $profile = new Profile;
            $student->setProfile($profile);
            // persist
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profile);
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('student/new.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/resend/{id}", name="resend_mail_student", methods={"GET", "POST"})
     * @ParamConverter("student", options={"id" = "id"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function sendAgain(Request $request, Student $student, ApplyRepository $applyRepository, RecruitRepository $recruitRepository, StudentChecker $checker, ApplyHelper $helper, SecurityHelper $securityHelper, RecruitHelper $recruitHelper)
    {
        // Si le formulaire est envoyé 
        if ($request->isMethod('POST')) {
            // resend confirmation email
            $securityHelper->reSend($student);
             // render 
             $this->addFlash('success', 'Lien envoyé');
        }
        // redirect 
        return $this->redirectToRoute('student_show', ['id' => $student->getId()]); 
    }

    /**
     * @Route("/{id}", name="student_show", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function show(Student $student, ApplyRepository $applyRepository, RecruitRepository $recruitRepository, StudentChecker $checker, ApplyHelper $helper, RecruitHelper $recruitHelper): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('student/show.html.twig', [
                'student' => $student,  
                'applies' => $helper->checkApplies('student', $student),    // open applies 
                'process' => $applyRepository->findByStudentProcess($student),  // processing applies 
                'fresh' => $applyRepository->findByStudentByFresh($student), // nb candidates
                'hired' => $helper->checkHired('student', $student), // confirm warning
                'freshRecruit' => $recruitRepository->findByStudentByFresh($student), // nb candidates
                'hiredRecruit' => $recruitHelper->checkHired('student', $student), // confirm warning 
            ]);
        }  
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function edit(Request $request, Student $student, ApplyRepository $applyRepository, RecruitRepository $recruitRepository, SecurityHelper $securityHelper, StudentHelper $studentHelper, RecruitHelper $recruitHelper, UploaderHelper $uploaderHelper, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {
            // get forms 
            $form = $this->createForm(UpdateStudentGeneralType::class, $student);
            $formDoc = $this->createForm(UpdateStudentDocType::class, $student);
            $formPassword = $this->createForm(StudentEditPasswordType::class, $student); 
            // check old password
            $oldPass = $student->getUser()->getPassword();
            // handle forms
            $form->handleRequest($request);
            $formPassword->handleRequest($request);
            $formDoc->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() || $formPassword->isSubmitted() && $formPassword->isValid() || $formDoc->isSubmitted() && $formDoc->isValid()) {
                // get student 
                $student = $form->getData();
                // upload identity picture 
                $uploadedFile = $form['pictures']->getData();
                $uploaderHelper->uploadEdit($uploadedFile, $student);
                // upload docs 
                if($request->files->get('update_student_doc') != null) { 
                    $studentHelper->editStudentDocs($formDoc, $form, $student, $request);
                }
                // edit user data 
                $securityHelper->editUser($formPassword->getData()->getUser(), $oldPass);
                // save and redirect
                $manager = $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Mise à jour réussie');
                return $this->redirectToRoute('student_edit', ['id' => $student->getId()]);
            }

            return $this->render('student/edit.html.twig', [
                'student' => $student,
                'form' => $form->createView(),
                'formDoc' => $formDoc->createView(),
                'formPassword' => $formPassword->createView(),
                'fresh' => $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                'freshRecruit' => $recruitRepository->findByStudentByFresh($student), // nb candidates
                'hiredRecruit' => $recruitHelper->checkHired('student', $student), // confirm warning
            ]);
        }   
    }

    /**
     * @Route("/{id}/{from}", name="student_delete", methods={"DELETE"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function delete(Request $request, Student $student, UploaderHelper $uploaderHelper, ApplyRepository $applyRepository, StudentChecker $checker, ApplyHelper $helper, $from): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            // delete private files when delete entity
            foreach($this->entities as $entity)
            {
                $get = 'get' . $entity;
                if($student->$get() != NULL) {
                    $fileName = $student->$get()->getFileName();
                    if($fileName) {
                        $uploaderHelper->deleteFile($fileName);
                    } 
                }   
            }
            // handle applies 
            $helper->handleStudentApplies($student); 
            // delete session
            $currentUserId = $this->getUser()->getId();
            if ($currentUserId == $student->getUser()->getId())
            {
                $session = $this->get('session');
                $session = new Session();
                $session->invalidate();
            }
            // save 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
            // redirect
            $this->addFlash('success', 'Compte Supprimé');
            return $this->redirectToRoute($from);
        }
    }
}
