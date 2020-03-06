<?php 

namespace App\Controller\University;

use App\Entity\School;
use App\Entity\Recruit;
use App\Entity\Student;
use App\Entity\Studies;
use App\Form\StudiesType;
use App\Repository\RecruitRepository;
use App\Repository\StudiesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/studies")
 */
class StudiesActionController extends AbstractController
{
   /**
     * @Route("/study/{id}/student/{student_id}/", name="recruit", methods={"POST"})
     * @IsGranted("ROLE_STUDENT_HIRED")
     * @ParamConverter("student", options={"id" = "student_id"})
     */
    public function recruit(Studies $studies, Student $student, Request $request, RecruitRepository $recruitRepository)
    {
        // check if apply is open to current offer
        // $hired = $repository->findBy(['offers' => $offers, 'hired' => 1]);
        // $agree = $repository->findBy(['offers' => $offers, 'agree' => 1]);
        // $confirmed = $repository->findBy(['offers' => $offers, 'confirmed' => 1]);
        // $finished = $repository->findBy(['offers' => $offers, 'finished' => 1]);

        // if($hired || $agree || $confirmed || $finished) {  
        //     $this->addFlash('error', 'Offre Indisponible');
        //     return $this->redirectToRoute('offers_index');
        // }

        // check if student is available
        // $hired2 = $repository->findBy(['student' => $student, 'hired' => 1]);
        // $agree2 = $repository->findBy(['student' => $student, 'agree' => 1]);
        // $confirmed2 = $repository->findBy(['student' => $student, 'confirmed' => 1]);
        // $finished2 = $repository->findBy(['student' => $student, 'finished' => 1]);

        // if($hired2 || $agree2 || $confirmed2) {
        //     $this->addFlash('error', 'Vous êtes déjà embauché ailleurs. Rendez-vous sur votre profil.');
        //     return $this->redirectToRoute('offers_show', ['id' => $offers->getId(), 'page' => $page]);
        // }

        // check if student have already applied to current studies 
        $recruit = $recruitRepository->findBy(['studies' => $studies, 'student' => $student]);

        if($recruit) {  

        //     $refused = $repository->checkIfrefusedExsists($offers, $student);
            
        //     if($refused) {
        //         $this->addFlash('error', 'Offre Indisponible');
        //         return $this->redirectToRoute('offers_show', ['id' => $offers->getId(), 'page' => $page]);
        //     }
        //     else {
                $this->addFlash('error', 'Vous avez déjà postulé');
                return $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);
        //     }  
        }

        // if($applies) {
        //     $this->addFlash('error', 'Offre Indisponible');
        //     return $this->redirectToRoute('offers_show', ['id' => $offers->getId(), 'page' => $page]);
        // }

        // send notification to company 
        // $email = $offers->getCompany()->getUser()->getEmail();
        // $name = $offers->getCompany()->getFirstname();
        // $offerTitle = $offers->getTitle();

        // $mailer->sendApplyMessage($email, $name, $offerTitle);

        $recruit = new Recruit; 
        // $apply->setHired(false);
        // $apply->setConfirmed(false);
        // $apply->setRefused(false);
        // $apply->setUnavailable(false);
        // $apply->setFinished(false);
        // $apply->setAgree(false);
        $recruit->setStudies($studies);
        $recruit->setStudent($student);

        if($this->isCsrfTokenValid('recruit'.$student->getId(), $request->request->get('_token'))) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($recruit);
            $manager->flush();
        }
        else {
            throw new \Exception('Demande Invalide');
        }

        $this->addFlash('success', 'Candidature enregistrée !');

        return $this->redirectToRoute('studies_show_recruit', ['id' => $studies->getId(), 'from' => 'student', 'from_id' => $student->getId()]);

    }
    
}