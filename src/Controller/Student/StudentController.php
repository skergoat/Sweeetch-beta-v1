<?php

namespace App\Controller\Student;

use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\Profile;
use App\Entity\Student;
use App\Form\StudentType;
use App\Entity\StudentCard;
use App\Entity\ProofHabitation;
use App\Service\UploaderHelper;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\UserRepository;
use App\Repository\ApplyRepository;
use App\Repository\ResumeRepository;
use App\Repository\StudentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $queryBuilder = $studentRepository->findAllPaginated("DESC");

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('student/index.html.twig', [
            'students' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="student_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): Response
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
    public function show(Student $student, applyRepository $applyRepository): Response
    {
        // $hired = $applyRepository->checkIfHired($student);
        // $fresh = $applyRepository->findByStudentByFresh($student);
        // $applies = $applyRepository->findByStudent($student);

        return $this->render('student/show.html.twig', [
            'student' => $student,
            'applies' => $applyRepository->findByStudent($student),
            'finished' => $applyRepository->findByStudentByFinished($student),
            'fresh' => $applyRepository->findByStudentByFresh($student),
            'hired' => $applyRepository->checkIfHired($student)
        ]);
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function edit(Request $request, Student $student, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper, ApplyRepository $applyRepository): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $student = $form->getData();

            // upload resume temporary
            $uploadedFile = $form['resume']['file']->getData();
            if($uploadedFile) {
                $newFilename = $uploaderHelper->uploadPrivateFile($uploadedFile, $student->getResume()->getFilename());

                $resume = $form->getData()->getResume();
                $resume->setFilename($newFilename);
                $resume->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                $resume->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');
                $student->setResume($resume);
            }

            $user = $form->getData()->getUser();
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));

            $manager = $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('student_edit', ['id' => $student->getId()]);
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
            'fresh' => $applyRepository->findByStudentByFresh($student),
            'hired' => $applyRepository->checkIfHired($student)
        ]);
    }

    /**
     * @Route("/{id}/{from}", name="student_delete", methods={"DELETE"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function delete(Request $request, Student $student, UploaderHelper $uploaderHelper, ApplyRepository $applyRepository, $from): Response
    {
        
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

            $entityManager->remove($student);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Compte Supprimé');

        return $this->redirectToRoute($from);
    }
}
