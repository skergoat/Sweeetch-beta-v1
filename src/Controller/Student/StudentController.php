<?php

namespace App\Controller\Student;

use App\Entity\User;
use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\Profile;
use App\Entity\Student;
use App\Form\StudentType;
use App\Entity\StudentCard;
use App\Entity\ProofHabitation;
use App\Form\UpdateStudentType;
use App\Service\UploaderHelper;
use Gedmo\Sluggable\Util\Urlizer;
use App\Form\UpdateStudentDocType;
use App\Form\UserEditPasswordType;
use App\Repository\UserRepository;
use App\Repository\ApplyRepository;
use App\Repository\ResumeRepository;
use App\Form\StudentEditPasswordType;
use App\Repository\StudentRepository;
use App\Form\UpdateStudentGeneralType;
use App\Service\UserChecker\AdminChecker;
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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{

    private $entities = ['Resume', 'IdCard', 'StudentCard', 'ProofHabitation'];

    /**
     * @Route("/", name="student_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(StudentRepository $studentRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // if($checker->adminValid($user)) 
        // {
            $queryBuilder = $studentRepository->findAllPaginated("DESC");

            $pagination = $paginator->paginate(
                $queryBuilder,
                $request->query->getInt('page', 1),
                10
            );

            return $this->render('student/index.html.twig', [
                'students' => $pagination,
            ]);
        // }
    }

    /**
     * @Route("/new/", name="student_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper, ValidatorInterface $validator): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $student = $form->getData();

            // get uploaded files name 
            $keys = array_keys($request->files->get('student'));
          
            foreach($keys as $key) {

                $uploadedFile = $form[$key]['file']->getData();

                $entity = $form[$key]->getName();
                $get = 'get' . ucfirst($entity); 
                $set = 'set' . ucfirst($entity);
                $class = "App\Entity\\" . ucfirst($entity);

                if($uploadedFile) {
                    $newFilename = $uploaderHelper->uploadPrivateFile($uploadedFile, $student->$get()->getFileName());
                    
                    $document = new $class;
                    $document->setFileName($newFilename);
                    $document->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                    $document->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');                    
                } 
                
                $student->$set($document);
            }  
            
            // set roles 
            $user = $student->getUser();

            // $user->setRoles(['ROLE_STUDENT', 'ROLE_NEW']);
            $user->setRoles(['ROLE_STUDENT']);
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));

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
     * @Route("/{id}", name="student_show", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function show(Student $student, ApplyRepository $applyRepository, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            return $this->render('student/show.html.twig', [
                'student' => $student,
                'applies' => $applyRepository->findByStudent($student),
                'finished' => $applyRepository->findBy(['student' => $student, 'finished' => true]),
                'fresh' => $applyRepository->findByStudentByFresh($student),
                // 'hired' => $applyRepository->checkIfHired($student)
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true])
            ]);
        }  
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function edit(Request $request, Student $student, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper, ApplyRepository $applyRepository, StudentChecker $checker): Response
    {
        if ($checker->studentValid($student)) {

            $form = $this->createForm(UpdateStudentGeneralType::class, $student);
            $formDoc = $this->createForm(UpdateStudentDocType::class, $student);
            $formPassword = $this->createForm(StudentEditPasswordType::class, $student); 

            // check old pass 
            $oldPass = $student->getUser()->getPassword();

            $form->handleRequest($request);
            $formPassword->handleRequest($request);
            $formDoc->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() || $formPassword->isSubmitted() && $formPassword->isValid() || $formDoc->isSubmitted() && $formDoc->isValid()) {

                $student = $form->getData();

                // get uploaded files name 
                if($request->files->get('update_student_doc') != null) {
                    $keys = array_keys($request->files->get('update_student_doc'));
                
                    foreach($keys as $key) {

                        $uploadedFile = $formDoc[$key]->getData();

                        $entity = $formDoc[$key]->getName();

                        switch($entity) {
                            case 'resumes':
                                $entity = 'resume';
                                $document = $form->getData()->getResume();
                            break;
                            case 'idCards':
                                $entity = 'idCard';
                                $document = $form->getData()->getIdCard();
                            break;
                            case 'studentCards':
                                $entity = 'studentCard';
                                $document = $form->getData()->getStudentCard();
                            break;
                            case 'proofHabitations':
                                $entity = 'proofHabitation';
                                $document = $form->getData()->getProofHabitation();
                            break;
                        }

                        $get = 'get' . ucfirst($entity); 
                        $set = 'set' . ucfirst($entity);
                        $class = "App\Entity\\" . ucfirst($entity);

                        if($uploadedFile) {
                            $newFilename = $uploaderHelper->uploadPrivateFile($uploadedFile, $student->$get()->getFileName());
                            
                            $document->setFileName($newFilename);
                            $document->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                            $document->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');                    
                        } 

                        if($uploadedFile != null) {
                            $student->$set($document);
                        } 
                    }
                }
                
                // edit password 
                $user = $formPassword->getData()->getUser();

                if($user->getPassword() != $oldPass)
                {
                    $user->setPassword($passwordEncoder->encodePassword(
                        $user,
                        $user->getPassword()
                    ));
                }

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
                // 'hired' => $applyRepository->checkIfHired($student)
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true])
            ]);

        }   
    }

    /**
     * @Route("/{id}/{from}", name="student_delete", methods={"DELETE"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function delete(Request $request, Student $student, UploaderHelper $uploaderHelper, ApplyRepository $applyRepository, StudentChecker $checker, $from): Response
    {
        if ($checker->studentValid($student)) {

            if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {

                // delete private files when delete entity
                foreach($this->entities as $entity)
                {
                    $get = 'get' . $entity;
                    $fileName = $student->$get()->getFileName();
                    if($fileName) {
                        $uploaderHelper->deleteFile($fileName);
                    } 
                }

                $entityManager = $this->getDoctrine()->getManager();

                $applies = $student->getApplies();
                
                foreach($applies as $applies) {

                    // send mail 
                    // $email = $student->getUser()->getEmail();
                    // $name = $student->getName();
                    // $offerTitle = $offers->getTitle();

                    // $mailer->sendDeleteCompanyMessage($email, $name, $offerTitle); 

                    if($applies->getFinished() == false) {
                        $entityManager->remove($applies);
                    }
                    else {
                        $applies->setStudent(NULL);
                    } 
                }

                // delete session
                $currentUserId = $this->getUser()->getId();
                if ($currentUserId == $student->getUser()->getId())
                {
                $session = $this->get('session');
                $session = new Session();
                $session->invalidate();
                }

                $entityManager->remove($student);
                $entityManager->flush();
            }

            $this->addFlash('success', 'Compte Supprimé');

            return $this->redirectToRoute($from);
        }
    }
}
