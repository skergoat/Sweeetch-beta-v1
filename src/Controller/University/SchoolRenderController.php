<?php

namespace App\Controller\University;

use App\Entity\School;
use App\Entity\Studies;
use App\Repository\StudiesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

 /**
 * @Route("/school")
 */
class SchoolRenderController extends AbstractController
{
     /**
     * @Route("/studies/index/{id}", name="school_studies_index", methods={"GET"})
     */
    public function index(StudiesRepository $studiesRepository, School $school): Response
    {
        return $this->render('studies/index.html.twig', [
            'studies' => $studiesRepository->findBy(['school' => $school]),
            'school' => $school
        ]);
    }

     /**
     * @Route("/studies/show/{id}/{school_id}", name="school_studies_show", methods={"GET"})
     * @ParamConverter("school", options={"id" = "school_id"})
     */
    public function show(Studies $study, School $school): Response
    {
        return $this->render('studies/show.html.twig', [
            'study' => $study,
            'school' => $school
        ]);
    }

}
