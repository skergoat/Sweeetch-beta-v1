<?php

namespace App\Controller\Company;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Pictures;
use App\Form\CompanyType;
use App\Form\UpdateCompanyType;
use App\Service\UploaderHelper;
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
        $queryBuilder = $companyRepository->findAllPaginated("DESC");

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('company/index.html.twig', [
            'companies' => $pagination,
        ]);

    }

    /**
     * @Route("/new", name="company_new", methods={"GET","POST"})
     */
    public function new(UserPasswordEncoderInterface $passwordEncoder, Request $request): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // set roles 
            $user = $company->getUser();
            $user->setRoles(['ROLE_COMPANY']);
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

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

            return $this->render('company/show.html.twig', [
                'company' => $company,  // company layout
                'applies' => $helper->checkApplies('offers', $offers),
                'hired' => $helper->checkHired('offers', $offers),  // show hired 
                'agree' => $helper->checkAgree('offers', $offers), // find agreed applies 
                'finished' => isset($array) ? $array : null, // find confirmed or finished applies 
                'candidates' => $helper->nbCandidates($offers), // show nb applies 
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="company_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function edit(Request $request, Company $company, UserPasswordEncoderInterface $passwordEncoder, OffersRepository $offersRepository, ApplyRepository $applyRepository, CompanyChecker $checker, UploaderHelper $uploaderHelper, ApplyHelper $helper): Response
    {
        if($checker->companyValid($company)) {

            $form = $this->createForm(UpdateCompanyType::class, $company);
            $formPassword = $this->createForm(CompanyEditPasswordType::class, $company); 

            // check old pass 
            $oldPass = $company->getUser()->getPassword();

            $form->handleRequest($request);
            $formPassword->handleRequest($request);

            if($form->isSubmitted() && $form->isValid() || $formPassword->isSubmitted() && $formPassword->isValid()) {

                $uploadedFile = $form['pictures']->getData();

                if($uploadedFile) {

                    if($company->getPictures() != null) {
                        $newFilename = $uploaderHelper->uploadFile($uploadedFile, $company->getPictures()->getFileName());
                    }
                    else {
                        $newFilename = $uploaderHelper->uploadFile($uploadedFile, null);
                    }

                    $document = new Pictures;
                    $document->setFileName($newFilename);
                    $document->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                    $document->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');                    
                } 
                
                $company->setPictures($document);

                // edit password 
                $user = $formPassword->getData()->getUser();

                if($user->getPassword() != $oldPass)
                {
                    $user->setPassword($passwordEncoder->encodePassword(
                        $user,
                        $user->getPassword()
                    ));
                }

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Mise à jour réussie');
                return $this->redirectToRoute('company_edit', ['id' => $company->getId() ]);
            }

            $offers = $offersRepository->findBy(['company' => $company]);

            return $this->render('company/edit.html.twig', [
                'company' => $company,
                'form' => $form->createView(),
                'formPassword' => $formPassword->createView(),
                // infos 
                'hired' => $helper->checkHired('offers', $offers),
                'agree' => $helper->checkAgree('offers', $offers),
                // 'confirmed' => $helper->checkConfirmed('offers', $offers),
                // 'finished' =>  $helper->checkFinished('offers', $offers),
                'candidates' => $helper->nbCandidates($offers),
            ]);
        }
    }

    /**
     * @Route("/{id}/{from}", name="company_delete", methods={"DELETE"})
     * @IsGranted("ROLE_COMPANY")
     */
    public function delete(Request $request, Company $company, ApplyRepository $repository, ApplyMailer $mailer, ApplyHelper $helper, $from): Response
    {
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            // get related offers 
            $offers = $company->getOffers();

            foreach($offers as $offers) {
                $applies = $offers->getApplies();

                // get related applies 
                foreach($applies as $applies) {
                    $student = $applies->getStudent();

                    // send mail 
                    // $email = $student->getUser()->getEmail();
                    // $name = $student->getName();
                    // $offerTitle = $offers->getTitle();
                    // $mailer->sendDeleteCompanyMessage($email, $name, $offerTitle); 

                    // if applies is agree, allow student to look for another job 
                    if($helper->checkConfirmed('offers', $offers) == []) {
                        $helper->available($applies->getOffers(), $applies->getStudent());
                    }

                    // delete offers or keep finished or confirmed applies  
                    if($helper->checkConfirmed('offers', $offers) == [] && $helper->checkFinished('offers', $offers) == []) {
                        // remove related applies 
                        $entityManager->remove($applies);
                    }
                    else {
                        $applies->setOffers(NULL);
                    } 
                }
                // remove related offers 
                $entityManager->remove($offers);
            }
            // delete session
            $currentUserId = $this->getUser()->getId();
            if ($currentUserId == $company->getUser()->getId())
            {
              $session = $this->get('session');
              $session = new Session();
              $session->invalidate();
            }
            // remove company 
            $entityManager->remove($company);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Compte Supprimé');
        return $this->redirectToRoute($from);
    }
}
