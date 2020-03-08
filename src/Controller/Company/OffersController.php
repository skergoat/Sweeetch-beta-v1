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
use App\Service\UserChecker\CompanyChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * @Route("/offers")
 */
class OffersController extends AbstractController
{
    /**
     * @Route("/page/{page<\d+>?1}", name="offers_index", methods={"GET"})
     */
    public function index(OffersRepository $offersRepository, PaginatorInterface $paginator, Request $request, $page): Response
    {
        $queryBuilder = $offersRepository->findBy(['state' => false], ['id' => 'desc']);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', $page),
            6
        );

        return $this->render('offers/index.html.twig', [
            'offers' => $pagination,
            'page' => $page
        ]);
    }

    /**
     * @Route("/new/{id}", name="offers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function new(Request $request, Company $company, ApplyRepository $applyRepository, OffersRepository $offersRepository, CompanyChecker $checker): Response
    {
        if($checker->companyValid($company)) {

            $offer = new Offers();
            $form = $this->createForm(OffersType::class, $offer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $offer = $form->getData();

                $offer->setCompany($company);
                $offer->setState(false);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($offer);
                $entityManager->flush();

                $this->addFlash('success', 'Emploi crée !');

                return $this->redirectToRoute('offers_new', ['id' => $company->getId()]);
            }

            $offers = $offersRepository->findBy(['company' => $company]);

            return $this->render('offers/new.html.twig', [
                'offers' => $offer,
                'form' => $form->createView(),
                'company' => $company,
                'hired' => $applyRepository->findBy(['offers' => $offers, 'hired' => 1]),
                'agree' => $applyRepository->findBy(['offers' => $offers, 'agree' => 1]),
                'applies' => $applyRepository->findBy(['offers' => $offers, 'finished' => 1]),
                'applyc' => $applyRepository->findBy(['offers' => $offers, 'refused' => 0, 'unavailable' => 0, 'confirmed' => 0, 'finished' => 0])     
            ]);
        }
    }

    /**
     * @Route("/{id}/{page<\d+>?1}", name="offers_show", methods={"GET"})
     */
    public function show(Offers $offer, ApplyRepository $applyRepository, AuthorizationCheckerInterface $authorizationChecker, $page): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) { // if ADMIN then ok 
        
            $hired = $applyRepository->findBy(['offers' => $offer, 'hired' => 1]);
            $agree = $applyRepository->findBy(['offers' => $offer, 'agree' => 1]);
            $confirmed = $applyRepository->findBy(['offers' => $offer, 'confirmed' => 1]);
            $finished = $applyRepository->findBy(['offers' => $offer, 'finished' => 1]);
    
            if($hired || $agree || $confirmed || $finished) {  // if there are already applies then ... 

                if ($authorizationChecker->isGranted('ROLE_SUPER_STUDENT')) { // if STUDENT then okk 
                
                    $student = $this->getUser()->getStudent();
                    $applied = $applyRepository->findAppliedIfExists($student, $offer);

                    if($applied) {
                        return $this->render('offers/show.html.twig', [ // if student = student.apply then ok 
                            'offers' => $offer,
                        ]);
                    }
                    else {
                        $this->addFlash('error', 'Vous n\'êtes pas autorisé à voir cette annonce');
                        return $this->redirectToRoute('offers_index');
                    }
        
                }
                else {
                    $this->addFlash('error', 'Vous n\'êtes pas autorisé à voir cette annonce');
                    return $this->redirectToRoute('offers_index');
                }
            }
        }

        return $this->render('offers/show.html.twig', [
            'offers' => $offer,
            'page' => $page
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
            $this->addFlash('error', 'Mission terminée');
            return $this->redirectToRoute('offers_company_index', ['id' => $company->getId()]);
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
                $this->addFlash('error', 'Mission terminée');
                return $this->redirectToRoute('offers_preview', ['id' => $offers->getId(), 'company' => $offers->getCompany()->getId()]);
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

        $this->addFlash('success', 'Offre supprimée !');

        return $this->redirectToRoute('offers_company_index', ['id' => $offer->getCompany()->getId()]);
    }
}
