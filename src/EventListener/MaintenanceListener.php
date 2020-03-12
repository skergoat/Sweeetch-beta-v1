<?php
namespace App\EventListener;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MaintenanceListener
{
    // private $container, $maintenance, $ipAuthorized, $twig;

    // public function __construct($maintenance, ContainerInterface $container, Environment $twig)
    // {
    //     $this->container = $container;
    //     $this->maintenance = $maintenance["statut"];
    //     $this->ipAuthorized = $maintenance["ipAuthorized"];
    //     $this->twig = $twig;
    // }
    // public function onKernelRequest(RequestEvent $event)
    // {
    //    // This will get the value of our maintenance parameter
    //     $maintenance = $this->maintenance ? $this->maintenance : false;
    //     $currentIP = $_SERVER['REMOTE_ADDR'];
    //     // This will detect if we are in dev environment (app_dev.php)
    //     // $debug = in_array($this->container->get('kernel')->getEnvironment(), ['dev']);
    //     // If maintenance is active and in prod environment
    //     if ($maintenance AND !in_array($currentIP, $this->ipAuthorized)) {
    //         // We load our maintenance template
    //         // $engine = $this->container->get('templating');
    //         // $template = $engine->render('maintenance/maintenance.html.twig');
    //         $page = $this->twig->render('maintenance/maintenance.html.twig');
    //         // We send our response with a 503 response code (service unavailable)
    //         $event->setResponse(new Response($page, 503));
    //         $event->stopPropagation();
    //     }
    // }
} 