<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityHelper
{
    private $manager;
    private $userMailer;
    // private $userRepository;
    private $tokenGenerator;
    private $router;

    public function __construct(EntityManagerInterface $manager, TokenGeneratorInterface $tokenGenerator, UserMailer $userMailer, UrlGeneratorInterface $router)
    {
        $this->manager = $manager;
        $this->mailer = $userMailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        // $this->userRepository = $userRepository;
    }

    public function createResetPasswordLink($user)
    {
        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On envoie une alerte disant que l'adresse e-mail est inconnue
            $this->addFlash('error', 'Cette adresse e-mail est inconnue');
            // On retourne sur la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // On génère un token
        $token = $this->tokenGenerator->generateToken();

        // On essaie d'écrire le token en base de données
        try{
            $user->setResetToken($token);
            // $this->manager->getDoctrine()->getManager();
            $this->manager->persist($user);
            $this->manager->flush();
        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('app_login');
        }

        // On génère l'URL de réinitialisation de mot de passe
        $url = $this->router->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
        // On génère l'e-mail
        $this->mailer->sendRecoverPassword($user, $url);
        
    }
}
