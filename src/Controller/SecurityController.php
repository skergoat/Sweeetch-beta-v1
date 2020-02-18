<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
}
