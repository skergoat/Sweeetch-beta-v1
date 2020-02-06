<?php

namespace App\Controller\Student;

use App\Entity\Resume;
use App\Entity\Profile;
use App\Entity\Student;
use App\Form\StudentType;
use App\Service\UploaderHelper;
use Gedmo\Sluggable\Util\Urlizer;
use App\Repository\UserRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/student")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/", name="student_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepository->findAll(),
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

           // upload resume temporary
            $uploadedFile = $form['resume']['file']->getData();
            if($uploadedFile) {
                $newFilename = $uploaderHelper->uploadFile($uploadedFile, $student->getResume()->getUrl());

                $resume = new Resume;
                $resume->setUrl($newFilename);
                $resume->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                $resume->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');
                $student->setResume($resume);
            }
            
            // set roles 
            $user = $student->getUser();
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
     *  @IsGranted("ROLE_STUDENT")
     */
    public function show(Student $student): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="student_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function edit(Request $request, Student $student, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $student = $form->getData();

            // upload resume temporary
            $uploadedFile = $form['resume']['file']->getData();
            if($uploadedFile) {
                $newFilename = $uploaderHelper->uploadFile($uploadedFile, $student->getResume()->getUrl());

                $resume = $form->getData()->getResume();
                $resume->setUrl($newFilename);
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
        ]);
    }

    /**
     * @Route("/{id}", name="student_delete", methods={"DELETE"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function delete(Request $request, Student $student): Response
    {
        if ($this->isCsrfTokenValid('delete'.$student->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($student);
            $entityManager->flush();
        }

        return $this->redirectToRoute('student_index');
    }
}
