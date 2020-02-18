<?php

namespace App\Controller\University;

use App\Entity\School;
use App\Form\SchoolType;
use App\Repository\SchoolRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/school")
 */
class SchoolActionsController extends AbstractController
{
    /**
     * @Route("/", name="school_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(SchoolRepository $schoolRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $schoolRepository->findAllPaginated("DESC");

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('school/index.html.twig', [
            'schools' => $pagination,
        ]);
    }

    /**
     * @Route("/student", name="school_student_index", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function indexByStudent(SchoolRepository $schoolRepository): Response
    {
        return $this->render('school/index_student.html.twig');
    }

    /**
     * @Route("/new", name="school_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $school = new School();
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $school = $form->getData();

             // set roles 
             $user = $school->getUser();
             // $user->setRoles(['ROLE_STUDENT', 'ROLE_NEW']);
             $user->setRoles(['ROLE_SCHOOL']);
             $user->setPassword($passwordEncoder->encodePassword(
                 $user,
                 $user->getPassword()
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($school);
            $entityManager->flush();

            return $this->redirectToRoute('school_index');
        }

        return $this->render('school/new.html.twig', [
            'school' => $school,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_show", methods={"GET"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function show(School $school): Response
    {
        return $this->render('school/show.html.twig', [
            'school' => $school,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="school_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function edit(Request $request, School $school): Response
    {
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('school_index');
        }

        return $this->render('school/edit.html.twig', [
            'school' => $school,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{from}", name="school_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function delete(Request $request, School $school, $from): Response
    {
        if ($this->isCsrfTokenValid('delete'.$school->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($school);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Compte Supprimé');

        return $this->redirectToRoute($from);
    }
}
