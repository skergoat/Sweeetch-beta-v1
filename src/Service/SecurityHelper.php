<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityHelper
{
    private $manager;
    private $userMailer;
    private $tokenGenerator;
    private $router;
    private $flash;

    public function __construct(EntityManagerInterface $manager, TokenGeneratorInterface $tokenGenerator, UserMailer $userMailer, UrlGeneratorInterface $router, FlashBagInterface $flash)
    {
        $this->manager = $manager;
        $this->mailer = $userMailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->flash = $flash;
        $this->router = $router;
    }

    public function createResetPasswordLink($user)
    {
        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On envoie une alerte disant que l'adresse e-mail est inconnue
            $this->flash->add('error', 'Cette adresse e-mail est inconnue');
            // On retourne sur la page de connexion
            // return $this->redirectToRoute('app_login');
            return new RedirectResponse($this->router->generate('app_login'));
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
            $this->flash->add('error', $e->getMessage());
            return new RedirectResponse($this->router->generate('app_login'));
        }
        // On génère l'URL de réinitialisation de mot de passe
        $url = $this->router->generate('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
        // On génère l'e-mail
        $this->mailer->sendAdminPassword($user, $url);  
        // On crée le message flash de confirmation
        $this->flash->add('success', 'E-mail de réinitialisation du mot de passe envoyé !');
    }
}
