<?php

namespace App\Controller\Student;

use App\Entity\Profile;
use App\Entity\Language;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="profile_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function edit(Request $request, Profile $profile): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profile_edit', ['id' => $profile->getId()]);
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }
}
