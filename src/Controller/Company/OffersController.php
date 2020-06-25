<?php

namespace App\Controller\Company;

use App\Entity\Offers;
use App\Entity\Company;
use App\Entity\Student;
use App\Form\OffersType;
use App\Form\FindOffersType;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use App\Repository\OffersRepository;
use App\Repository\StudentRepository;
use App\Service\Recruitment\ApplyHelper;
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


class OffersController extends AbstractController
{
    /**
     * @Route("offers/page/{page<\d+>?1}", name="offers_index", methods={"GET"})
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
     * @Route("/find/page/{page<\d+>?1}", name="find", methods={"GET"})
     */
    public function find(OffersRepository $offersRepository, PaginatorInterface $paginator, Request $request, $page): Response
    {
        $domain = $request->get('domain');

        if($domain == 'tous') {
            $queryBuilder = $offersRepository->findAll();
        }
        else {
            $queryBuilder = $offersRepository->findBy(['state' => false, 'domain' => $domain], ['domain' => 'desc']);
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', $page),
            6
        );

        return $this->render('offers/find.html.twig', [
            'offers' => $pagination,
            'page' => $page
        ]);
    }

    /**
     * @Route("company/offers/new/{id}", name="offers_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function new(Request $request, Company $company, ApplyRepository $applyRepository, OffersRepository $offersRepository, CompanyChecker $checker, ApplyHelper $helper): Response
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
                // infos
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                'closed' =>  $helper->checkOfferFinished($offers),
                'candidates' => $helper->nbCandidates($offers),    
            ]);
        }
    }

    /**
     * @Route("offers/{id}/{page<\d+>?1}", name="offers_show", methods={"GET"})
     */
    public function show(Offers $offer, ApplyRepository $applyRepository, AuthorizationCheckerInterface $authorizationChecker, ApplyHelper $helper, $page): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_ADMIN')) { // if ADMIN then ok 
    
            if($helper->checkHired('offers', $offer) || $helper->checkAgree('offers', $offer) || $helper->checkConfirmed('offers', $offer) || $helper->checkFinished('offers', $offer)) {  // if there are already applies then ... 

                if ($authorizationChecker->isGranted('ROLE_SUPER_STUDENT')) { // if STUDENT then ok
                
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
     * @Route("company/offers/edit/{id}/{company}", name="offers_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_COMPANY")
     * @ParamConverter("company", options={"id" = "company"})
     */
    public function edit(Request $request, Offers $offer, ApplyRepository $repository, OffersRepository $offersRepository, Company $company, CompanyChecker $checker, ApplyHelper $helper): Response
    {
        if($checker->companyOffersValid($company, $offer)) {
            // prevent user from deleting finished offer 
            if($helper->checkConfirmed('offers', $offer) || $helper->checkFinished('offers', $offer)) {
                $this->addFlash('error', 'Mission terminée');
                return $this->redirectToRoute('offers_company_index', ['id' => $company->getId()]);
            }   
    
            $form = $this->createForm(OffersType::class, $offer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // edit 
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Mise à jour réussie !');
                return $this->redirectToRoute('offers_edit', ['id' => $offer->getId(), 'company' => $company->getId()]);
            }

            // get and render offers infos 
            $offers = $offersRepository->findBy(['company' => $company], ['id' => 'desc']);

            return $this->render('offers/edit.html.twig', [
                'offers' => $offer,
                // test 
                'applies' => $repository->findBy(['offers' => $offer, 'refused' => false, 'unavailable' => false, 'confirmed' => false, 'finished' => false], ['date_recruit' => 'desc']),
                // test
                'company' => $company,
                'form' => $form->createView(),
                // infos
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                'closed' =>  $helper->checkOfferFinished($offers),
                'candidates' => $helper->nbCandidates($offers),   
            ]);
        }
    }

    /**
     * @Route("/{id}", name="offers_delete", methods={"DELETE"}) *****git 
     * @IsGranted("ROLE_SUPER_COMPANY")
     */
    public function delete(Request $request, Offers $offer, ApplyRepository $repository, ApplyMailer $mailer, ApplyHelper $helper): Response
    {
        // prevent user from deleting finished offer 
        if($helper->checkConfirmed('offers', $offer) || $helper->checkFinished('offers', $offer)) {
            $this->addFlash('error', 'Mission terminée');
            return $this->redirectToRoute('offers_edit', ['id' => $offer->getId(), 'company' => $offer->getCompany()->getId()]);
        }
        
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            // delete related applies
            $helper->handleOffersApplies($offer);
            // delete
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offre supprimée !');
            return $this->redirectToRoute('offers_company_index', ['id' => $offer->getCompany()->getId()]);
        }
        else {
            $this->addFlash('error', 'Requête Invalide');
            return $this->redirectToRoute('offers_company_index', ['id' => $offer->getCompany()->getId()]);
        }    
    }
}
