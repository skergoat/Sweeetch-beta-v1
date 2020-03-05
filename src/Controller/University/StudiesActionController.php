<?php 

namespace App\Controller\University;

use App\Entity\School;
use App\Entity\Studies;
use App\Form\StudiesType;
use App\Repository\StudiesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/studies")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class StudiesActionController extends AbstractController
{
    

}