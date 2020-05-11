<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Service\Mailer\UserMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityHelper
{
    private $manager;
    private $userMailer;
    private $tokenGenerator;
    private $router;
    private $flash;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $manager, TokenGeneratorInterface $tokenGenerator, UserMailer $userMailer, UrlGeneratorInterface $router, FlashBagInterface $flash, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->mailer = $userMailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->flash = $flash;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository ;
    }

    public function createResetPasswordLink($user, $bool)
    {
        // Si l'utilisateur n'existe pas
        if ($user === null) {
            // On envoie une alerte disant que l'adresse e-mail est inconnue
            $this->flash->add('error', 'Cette adresse e-mail est inconnue');
            // On retourne sur la page de connexion
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
        if($bool) {
            $this->mailer->sendRecoverPassword($user, $url); 
        }
        else {
            $this->mailer->sendAdminPassword($user, $url);
        }
        // On crée le message flash de confirmation
        $this->flash->add('success', 'E-mail de réinitialisation du mot de passe envoyé !');
    }

    // new user 
    public function newUser($entity, $role)
    {
        // set user and roles
        $user = $entity->getUser();
        $user->setRoles([$role]);
        $user->setConfirmed(false);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $user->getPassword()
        ));
        // create token 
        $user->setActivateToken(md5(uniqid()));
        // generate email 
        $this->mailer->sendActivate($user);
        // send notifs to admins 
        $admins = $this->userRepository->findByAdmin("ROLE_ADMIN");
        foreach($admins as $admins) {
            if($role == "ROLE_STUDENT") {
                $this->mailer->sendNewUser($admins, $entity);
            }
            else {
                $this->mailer->sendNewUsers($admins, $entity);
            }
        }
    }

    // resend confirmation email
    public function reSend($entity)
    {   
        // dd($entity);
        // generate email 
        $user = $entity->getUser();
        $this->mailer->sendActivate($user);
    }

    // edit user password 
    public function editUser($user, $oldPass)
    {
        if($user->getPassword() != $oldPass)
        {
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            ));
        }
    }
}
