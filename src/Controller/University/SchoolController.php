<?php

namespace App\Controller\University;

use App\Entity\User;
use App\Entity\School;
use App\Entity\Student;
use App\Entity\Pictures;
use App\Form\SchoolType;
use App\Form\UpdateSchoolType;
use App\Service\SecurityHelper;
use App\Service\UploaderHelper;
use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
use App\Repository\ApplyRepository;
use App\Form\SchoolEditPasswordType;
use App\Repository\SchoolRepository;
use App\Repository\RecruitRepository;
use App\Repository\StudiesRepository;
use App\Service\UserChecker\AdminChecker;
use App\Service\Recruitment\RecruitHelper;
use App\Service\UserChecker\SchoolChecker;
use App\Service\UserChecker\StudentChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/school")
 */
class SchoolController extends AbstractController
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
     * @Route("/new", name="school_new", methods={"GET","POST"})
     */
    public function new(Request $request, SecurityHelper $securityHelper): Response
    {
        $school = new School();
        $form = $this->createForm(SchoolType::class, $school);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get school
            $school = $form->getData();
            // set user
            $securityHelper->newUser($school, 'ROLE_SCHOOL');
            // save
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($school);
            $entityManager->flush();
            // render 
            return $this->redirectToRoute('app_login');
        }
        // render 
        return $this->render('school/new.html.twig', [
            'school' => $school,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="school_show", methods={"GET"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function show(School $school, StudiesRepository $studiesRepository, RecruitRepository $recruitRepository, SchoolChecker $checker, RecruitHelper $recruitHelper): Response
    {
        if ($checker->schoolValid($school)) {
            // get school studies 
            $studies = $studiesRepository->findBy(['school' => $school]);
            // render 
            return $this->render('school/show.html.twig', [
                'school' => $school,
                'studies' => $studiesRepository->findBy(['school' => $school], ['id' => 'desc']),
                'recruits' => $recruitRepository->findBy([
                    'studies' => $studies,
                    'hired' => false,
                    'agree' => false,
                    'refused' => false,
                    'unavailable' => false,
                    'finished' => false,
                ], ['date_recruit' => 'desc']),
                'hired' => $recruitRepository->findBy(['studies' => $studies, 'hired' => true],['date_recruit' => 'desc']),
                'agree' => $recruitRepository->findBy(['studies' => $studies, 'agree' => true],['date_recruit' => 'desc']), 
                'candidates' => $recruitHelper->nbCandidates($studies), // show nb applies 
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="school_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function edit(Request $request, School $school, UserPasswordEncoderInterface $passwordEncoder, RecruitRepository $recruitRepository, StudiesRepository $studiesRepository, RecruitHelper $recruitHelper, UploaderHelper $uploaderHelper, SecurityHelper $securityHelper, SchoolChecker $checker): Response
    {
        if ($checker->schoolValid($school)) {
            // get form 
            $form = $this->createForm(UpdateSchoolType::class, $school);
            $formPassword = $this->createForm(SchoolEditPasswordType::class, $school); 
            // check old pass 
            $oldPass = $school->getUser()->getPassword();
            // handle form 
            $form->handleRequest($request);
            $formPassword->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() || $formPassword->isSubmitted() && $formPassword->isValid()) {
                // edit user data 
                $securityHelper->editUser($formPassword->getData()->getUser(), $oldPass);
                // upload file 
                $uploadedFile = $form['pictures']->getData();
                $uploaderHelper->uploadEdit($uploadedFile, $school);
                // save 
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Mise à jour réussie');
                return $this->redirectToRoute('school_edit', ['id' => $school->getId() ]);
            }
            // get studies
            $studies = $studiesRepository->findBy(['school' => $school]);
            // render 
            return $this->render('school/edit.html.twig', [
                'school' => $school,
                'form' => $form->createView(),
                'formPassword' => $formPassword->createView(),
                'hired' => $recruitRepository->findBy(['studies' => $studies, 'hired' => true],['date_recruit' => 'desc']),
                'agree' => $recruitRepository->findBy(['studies' => $studies, 'agree' => true],['date_recruit' => 'desc']), 
                'candidates' => $recruitHelper->nbCandidates($studies), // show nb applies 
            ]);
        }
    }

    /**
     * @Route("/{id}/{from}", name="school_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SCHOOL")
     */
    public function delete(Request $request, School $school, RecruitHelper $helper, $from): Response
    {
        if ($this->isCsrfTokenValid('delete'.$school->getId(), $request->request->get('_token'))) {
            // handle recruit 
            $helper->handleDeleteSchool($school);
            // delete session
            $currentUserId = $this->getUser()->getId();
            if ($currentUserId == $school->getUser()->getId())
            {
              $session = $this->get('session');
              $session = new Session();
              $session->invalidate();
            }
            // delete
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($school);
            $entityManager->flush();

            $this->addFlash('success', 'Compte Supprimé');
            return $this->redirectToRoute($from);
        }
        else {
            $this->addFlash('error', 'Requête Invalide');
            return $this->redirectToRoute($from);
        }
    }
}
