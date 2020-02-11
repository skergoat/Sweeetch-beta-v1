<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Form\ApplyType;
use App\Repository\ApplyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apply")
 */
class ApplyController extends AbstractController
{
    /**
     * @Route("/", name="apply_index", methods={"GET"})
     */
    public function index(ApplyRepository $applyRepository): Response
    {
        return $this->render('apply/index.html.twig', [
            'applies' => $applyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="apply_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $apply = new Apply();
        $form = $this->createForm(ApplyType::class, $apply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($apply);
            $entityManager->flush();

            return $this->redirectToRoute('apply_index');
        }

        return $this->render('apply/new.html.twig', [
            'apply' => $apply,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="apply_show", methods={"GET"})
     */
    public function show(Apply $apply): Response
    {
        return $this->render('apply/show.html.twig', [
            'apply' => $apply,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="apply_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Apply $apply): Response
    {
        $form = $this->createForm(ApplyType::class, $apply);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('apply_index');
        }

        return $this->render('apply/edit.html.twig', [
            'apply' => $apply,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="apply_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Apply $apply): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apply->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($apply);
            $entityManager->flush();
        }

        return $this->redirectToRoute('apply_index');
    }
}
