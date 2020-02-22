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
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(OffersRepository $offersRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $offersRepository->findAllPaginated("DESC");

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('offers/index.html.twig', [
            'offers' => $pagination,
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

            $this->addFlash('success', 'Emploi crée !');

            return $this->redirectToRoute('offers_new', ['id' => $company->getId()]);
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
     * @Route("/{id}/edit/{company}", name="offers_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company"})
     */
    public function edit(Request $request, Offers $offer, ApplyRepository $repository, Company $company): Response
    {
        $finished = $repository->checkIfFinished($offer);

        if($finished) {
            throw new \Exception('already finished');
        }

        $form = $this->createForm(OffersType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Mise à jour réussie !');

            return $this->redirectToRoute('offers_edit', ['id' => $offer->getId(), 'company' => $company->getId()]);
        }

        return $this->render('offers/edit.html.twig', [
            'offers' => $offer,
            'company' => $company,
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

            $finished = $repository->checkIfFinished($offer);

            if($finished) {
                throw new \Exception('already finished');
            }

            $entityManager = $this->getDoctrine()->getManager();

            $applies = $repository->findBy(['offers' => $offer]);
            
            foreach($applies as $applies) 
            {
                $student = $applies->getStudent();

                // set roles 
                $student->getUser()->setRoles([
                    "ROLE_SUPER_STUDENT"
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
                $mailer->sendDeleteMessage($email, $name, $offer->getTitle());
                
                if($applies->getFinished() == false) {
                    $entityManager->remove($applies);
                }
                else {
                    $applies->setOffers(NULL);
                }
            }
            
            // delete offers 
            $entityManager->remove($offer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offers_index');
    }
}
