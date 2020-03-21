<?php 

// listen and generate "confirm email" message 

namespace App\EventListener;

use App\Activate\ActivateHTMLAdder;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ActivateListener
{
    protected $activateHTML;
    private $security;
  
    public function __construct(ActivateHTMLAdder $activateHTML, Security $security)
    {
      $this->activateHTML = $activateHTML;
      $this->security = $security;
    }

    public function processActivate(ResponseEvent $event)
    {
        $response = $event->getResponse();

        $path = $event->getRequest()->getPathInfo();

        // check if isset user 
        if($this->security->getToken() != null) {
            // check if user is connected and has not activate account
            if($this->security->getToken()->getUser() != 'anon.' && $this->security->getUser()->getActivateToken() != null) {    
                // display warning message 
                if(
                    preg_match('/^\/company\/.{1,}/', $path) == true
                ||  preg_match('/^\/student\/.{1,}/', $path) == true
                ||  preg_match('/^\/school\/.{1,}/', $path) == true
                ){
                    $this->activateHTML->addActivate($response, $this->security->getToken()->getUser());
                }       
            }
        }   
    }
}