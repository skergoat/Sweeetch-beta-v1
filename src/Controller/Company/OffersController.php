<?php

namespace App\Controller\Company;

use App\Entity\Offers;
use App\Entity\Company;
use App\Entity\Student;
use App\Form\OffersType;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use App\Repository\OffersRepository;
use App\Repository\StudentRepository;
use App\Controller\Company\ApplyController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/offers")
 */
class OffersController extends AbstractController
{
    /**
     * @Route("/", name="offers_index", methods={"GET"})
     */
    public function index(OffersRepository $offersRepository): Response
    {
        return $this->render('offers/index.html.twig', [
            'offers' => $offersRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new/{id}", name="offers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function new(Request $request, Company $company): Response
    {
        $offer = new Offers();
        $form = $this->createForm(OffersType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $offer = $form->getData();

            $offer->setCompany($company);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offer);
            $entityManager->flush();

            return $this->redirectToRoute('offers_company_index', ['id' => $company->getId()]);
        }

        return $this->render('offers/new.html.twig', [
            'offers' => $offer,
            'form' => $form->createView(),
            'company' => $company
        ]);
    }

    /**
     * @Route("/{id}", name="offers_show", methods={"GET"})
     */
    public function show(Offers $offer): Response
    {
        return $this->render('offers/show.html.twig', [
            'offers' => $offer,
        ]);
    }

    /**
     * @Route("/preview/{id}", name="offers_preview", methods={"GET"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function showPreview(ApplyRepository $applyRepository, Offers $offer): Response
    {   
        // $applies = $offer->getApplies();
        $applies = $applyRepository->findByOffer($offer);
       
        return $this->render('offers/show_preview.html.twig', [
            'offers' => $offer,
            'applies' => $applies
        ]);
    }

    /**
     * @Route("/{id}/edit", name="offers_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function edit(Request $request, Offers $offer): Response
    {
        $form = $this->createForm(OffersType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('offers_index');
        }

        return $this->render('offers/edit.html.twig', [
            'offers' => $offer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="offers_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function delete(Request $request, Offers $offer, ApplyRepository $repository, ApplyMailer $mailer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            $applies = $repository->findBy(['offers' => $offer]);
            
            foreach($applies as $applies) 
            {
                $student = $applies->getStudent();

                // set roles 
                $student->getUser()->setRoles([
                    "ROLE_SUPER_STUDENT",
                    "ROLE_TO_APPLY"
                ]);

                     // set other student offers to unavailable
                $unavailables = $repository->setToUnavailables($offer, $student);

                foreach($unavailables as $unavailables) {

                    if($unavailables->getUnavailable() == true) {
                        $unavailables->setUnavailable(false);
                    } 
                }

                // send mail 
                $email = $student->getUser()->getEmail();
                $name = $student->getName();
                $mailer->sendDeleteCompanyMessage($email, $name, $offer->getTitle()); 
               
                $entityManager->remove($applies);
            }

            // delete offers 
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offers_index');
    }
}
