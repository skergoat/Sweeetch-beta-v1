<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class LoginListener
{
    private $urlGenerator;
    private $security;
    
    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event)
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
        }

    }
}