<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\SecurityHelper;
use App\Repository\UserRepository;
use App\Repository\ApplyRepository;
use App\Service\UserChecker\AdminChecker;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/index", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $userRepository->findByRolePaginated('ROLE_ADMIN');

        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder, SecurityHelper $helper): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // set roles 
            $user = $form->getData();
            $user->setRoles(['ROLE_ADMIN']);
            $user->setConfirmed(true);
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form['password']->getData()
            ));
            // save 
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // create link to change password 
            $helper->createResetPasswordLink($user);
            // redirect
            $this->addFlash('success', 'Admin crée');
            return $this->redirectToRoute('user_new');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="admin", methods={"GET"})
     */
    public function show(UserRepository $userRepository, ApplyRepository $applyRepository, AdminChecker $checker): Response
    {   
        return $this->render('back/index.html.twig', [
            'students' => $userRepository->findByRole('ROLE_STUDENT'),
            'company' => $userRepository->findByRole('ROLE_COMPANY'),
            'school' => $userRepository->findByRole('ROLE_SCHOOL'),
            'applies' => $applyRepository->getHired()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, AdminChecker $checker): Response
    {
        if($checker->adminValid($user)) 
        {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                // $user = $form->getData();
                $user->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                ));

                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'Admin modifiée');

                // granted user to redirect 
                return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Admin supprimée');

        return $this->redirectToRoute('user_index');
    }
}
