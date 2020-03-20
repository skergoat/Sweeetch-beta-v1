<?php

namespace App\Controller\Company;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Pictures;
use App\Form\CompanyType;
use App\Form\UpdateCompanyType;
use App\Service\SecurityHelper;
use App\Service\UploaderHelper;
use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
use App\Repository\ApplyRepository;
use App\Service\Mailer\ApplyMailer;
use App\Repository\OffersRepository;
use App\Form\CompanyEditPasswordType;
use App\Repository\CompanyRepository;
use App\Service\Recruitment\ApplyHelper;
use App\Service\UserChecker\AdminChecker;
use App\Service\UserChecker\CompanyChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/company")
 */
class CompanyController extends AbstractController
{
    /**
     * @Route("/", name="company_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(CompanyRepository $companyRepository, PaginatorInterface $paginator, Request $request): Response
    {   
        // get companies
        $queryBuilder = $companyRepository->findAllPaginated("DESC");
        // paginate 
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        // render 
        return $this->render('company/index.html.twig', [
            'companies' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="company_new", methods={"GET","POST"})
     */
    public function new(Request $request, SecurityHelper $securityHelper): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // set user
            $securityHelper->newUser($company, 'ROLE_COMPANY');
            // save 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();
            // render 
            return $this->redirectToRoute('app_login');
        }
        // render 
        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_show", methods={"GET"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function show(Company $company, OffersRepository $offersRepository, ApplyRepository $applyRepository, CompanyChecker $checker, ApplyHelper $helper): Response
    {
        if($checker->companyValid($company)) {
            // get company offers 
            $offers = $offersRepository->findBy(['company' => $company]);
            // get finished or confirmed applies 
            $array = $helper->findByOffersFinished($offers);
            // render 
            return $this->render('company/show.html.twig', [
                'company' => $company,  // company layout
                'offers' => $offersRepository->findBy(['company' => $company], ['id' => 'desc']),
                'applies' => $helper->checkApplies('offers', $offers),
                'hired' => $applyRepository->findBy(['offers' => $offers, 'hired' => true],['date_recruit' => 'desc']),  // show hired 
                'agree' => $applyRepository->findBy(['offers' => $offers, 'agree' => true],['date_recruit' => 'desc']), // find agreed applies 
                'finished' => isset($array) ? $array : null, // find confirmed or finished applies 
                'candidates' => $helper->nbCandidates($offers), // show nb applies 
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="company_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function edit(Request $request, Company $company, OffersRepository $offersRepository, CompanyChecker $checker, UploaderHelper $uploaderHelper, ApplyHelper $helper, SecurityHelper $securityHelper): Response
    {
        if($checker->companyValid($company)) {
            // get forms 
            $form = $this->createForm(UpdateCompanyType::class, $company);
            $formPassword = $this->createForm(CompanyEditPasswordType::class, $company); 
            // check old pass 
            $oldPass = $company->getUser()->getPassword();
            $form->handleRequest($request);
            $formPassword->handleRequest($request);
            // send form 
            if($form->isSubmitted() && $form->isValid() || $formPassword->isSubmitted() && $formPassword->isValid()) {
                // get uploaded file 
                $uploadedFile = $form['pictures']->getData();
                $uploaderHelper->uploadEdit($uploadedFile, $company);
                // edit user data 
                $securityHelper->editUser($formPassword->getData()->getUser(), $oldPass);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Mise à jour réussie');
                return $this->redirectToRoute('company_edit', ['id' => $company->getId() ]);
            }
            // get offers
            $offers = $offersRepository->findBy(['company' => $company]);
            // render 
            return $this->render('company/edit.html.twig', [
                'company' => $company,
                'form' => $form->createView(),
                'formPassword' => $formPassword->createView(),
                // infos 
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                'candidates' => $helper->nbCandidates($offers),
            ]);
        }
    }

    /**
     * @Route("/{id}/{from}", name="company_delete", methods={"DELETE"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function delete(Request $request, Company $company, ApplyHelper $helper, $from): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            // handle applies 
            $helper->handleCompanyApplies($company);
            // delete session
            $currentUserId = $this->getUser()->getId();
            if ($currentUserId == $company->getUser()->getId())
            {
              $session = $this->get('session');
              $session = new Session();
              $session->invalidate();
            }
            // remove company 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($company);
            $entityManager->flush();
            // render 
            $this->addFlash('success', 'Compte Supprimé');
            return $this->redirectToRoute($from);
        }
        else {
            $this->addFlash('error', 'Requête Invalide');
            return $this->redirectToRoute($from);
        }     
    }
}
