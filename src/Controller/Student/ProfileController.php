<?php

namespace App\Controller\Student;

use App\Entity\Profile;
use App\Entity\Student;
use App\Entity\Language;
use App\Form\ProfileType;
use App\Repository\ApplyRepository;
use App\Repository\ProfileRepository;
use App\Repository\RecruitRepository;
use App\Service\Recruitment\RecruitHelper;
use App\Service\UserChecker\StudentChecker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/student")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/edit/{id}/{student_id}", name="profile_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STUDENT")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function edit(Request $request, Profile $profile, Student $student, ApplyRepository $applyRepository, RecruitRepository $recruitRepository, RecruitHelper $recruitHelper, StudentChecker $checker): Response
    {
        if ($checker->studentProfileValid($student, $profile)) {

            $form = $this->createForm(ProfileType::class, $profile);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Mise à jour réussie');

                return $this->redirectToRoute('profile_edit', ['id' => $profile->getId(), 'student_id' => $student->getId()]);
            }

            return $this->render('profile/edit.html.twig', [
                'profile' => $profile,
                'form' => $form->createView(),
                'student' => $student,
                'fresh' =>  $applyRepository->findByStudentByFresh($student),
                'hired' => $applyRepository->findBy(['student' => $student, 'hired' => true]),
                'freshRecruit' => $recruitRepository->findByStudentByFresh($student), // nb candidates
                'hiredRecruit' => $recruitHelper->checkHired('student', $student), // confirm warning
            ]);
        }  
    }
}
