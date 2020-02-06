<?php

namespace App\Controller\Test;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render("Front/test.html.twig");
    }

    /**
     * @Route("/form", name="form", methods={"POST"})
     */
    public function form(Request $request)
    {
        $return = $request->request->get('name');

        return new Response('ok ca roule !');

    }

}
