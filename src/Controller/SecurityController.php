<?php

namespace App\Controller;

use App\Form\RecoverType;
use App\Form\ResetPassType;
use App\Form\UserEditPasswordType;
use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
// use App\Service\Mailer\ForgottenMailer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;
// use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    private $security;
    
    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->security->getUser();
        
        if ($user) {

            if($this->security->isGranted('ROLE_ADMIN')) {
                return new RedirectResponse($this->urlGenerator->generate('admin'));
            }
            else if($this->security->isGranted('ROLE_STUDENT')) {
                $id = $user->getStudent()->getId(); 
                return new RedirectResponse($this->urlGenerator->generate('student_show', ['id' => $id])); 
            }
            else if($this->security->isGranted('ROLE_COMPANY')) {
                $id = $user->getCompany()->getId(); 
                return new RedirectResponse($this->urlGenerator->generate('company_show', ['id' => $id])); 
            }
            if($this->security->isGranted('ROLE_SCHOOL')) {
                $id = $user->getSchool()->getId(); 
                return new RedirectResponse($this->urlGenerator->generate('school_show', ['id' => $id])); 
            }
        }
       
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

     /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $users)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $users->findOneBy(['activate_token' => $token]);
        // Si aucun utilisateur n'est associé à ce token
        if(!$user){
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        // On supprime le token
        $user->setActivateToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        // On génère un message
        $this->addFlash('message', 'Utilisateur activé avec succès');
        // On retourne à l'accueil
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/oubli-pass", name="app_forgotten_password")
     */
    public function oubliPass(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, UserMailer $mailer): Response
    {
        // On initialise le formulaire
        $form = $this->createForm(ResetPassType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {

            // On récupère les données
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $userRepository->findOneBy(['email' => $donnees['email']]);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                $this->addFlash('error', 'Cette adresse e-mail est inconnue');
                // On retourne sur la page de connexion
                return $this->redirectToRoute('app_login');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'écrire le token en base de données
            try{
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            // On génère l'e-mail
            $mailer->sendRecoverPassword($user, $url);

            // On crée le message flash de confirmation
            $this->addFlash('success', 'E-mail de réinitialisation du mot de passe envoyé !');

            // On redirige vers la page de login
            return $this->redirectToRoute('app_login');
        }

        // On envoie le formulaire à la vue
        return $this->render('security/forgotten_password.html.twig',['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/reset_pass/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        // On cherche un utilisateur avec le token donné
        $user = $this->getDoctrine()->getRepository('App\Entity\User')->findOneBy(['reset_token' => $token]);

        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On affiche une erreur
            $this->addFlash('error', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

         // On initialise le formulaire
         $form = $this->createForm(RecoverType::class);
         // On traite le formulaire
         $form->handleRequest($request);

        // Si le formulaire est envoyé 
        if ($request->isMethod('POST')) {
            
            // same password
            if($form->getData() == null) {
                return $this->render('security/reset_password.html.twig', ['token' => $token, 'form' => $form->createView()]);
            }

            // password null
            if($form->getData()['password'] == null) {
                $this->addFlash('error', 'Veuillez entrer un mot de passe, svp');
                return $this->render('security/reset_password.html.twig', ['token' => $token, 'form' => $form->createView()]);
            }

            // pattern 
            $violations = $validator->validate(
                $form->getData()['password'],
                new Regex([
                    'pattern' => '/^(?=.*[A-Z]+)(?=.*[a-z]+)(?=.*[0-9]+)\S{8,30}$/',
                    'message' => 'Le mot de passe doit contenir au moins 1 majuscule, 1 caractère spécial et 1 chiffre'
                ]),
            );

            if ($violations->count() > 0) {
                $violation = $violations[0];
                $this->addFlash('error', $violation->getMessage());
                return $this->render('security/reset_password.html.twig', ['token' => $token, 'form' => $form->createView()]);
            }

            // On supprime le token
            $user->setResetToken(null);
            // On chiffre le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $form->getData()['password']));
            // On stocke
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // On crée le message flash
            $this->addFlash('success', 'Mot de passe mis à jour');
            // On redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        } else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', ['token' => $token, 'form' => $form->createView()]);
        }
    }
}
