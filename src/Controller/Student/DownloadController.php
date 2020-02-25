<?php

namespace App\Controller\Student;

use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\StudentCard;
use App\Service\UserChecker;
use App\Entity\ProofHabitation;
use App\Service\UploaderHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DownloadController extends AbstractController
{   
    public function downloadDocuments($resume, UploaderHelper $uploaderHelper) 
    {
        // $resume = $reference->getArticle();
        // $this->denyAccessUnlessGranted('STUDENT', $article);

        $response = new StreamedResponse(function() use ($resume, $uploaderHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploaderHelper->readStream($resume->getFileName(), false);

            stream_copy_to_stream($fileStream, $outputStream);
        });

        // $disposition = HeaderUtils::makeDisposition(
        //     HeaderUtils::DISPOSITION_ATTACHMENT,
        //     $resume->getOriginalFilename()
        // );

        $response->headers->set('Content-Type', $resume->getMimeType());
        // $response->headers->set('Content-Disposition', $disposition);
        return $response;
        
    }

    /**
     * @Route("/resume/{id}/download", name="student_download_resume", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function downloadResume(Resume $resume, UploaderHelper $uploaderHelper, UserChecker $checker)
    {
       if($checker->documentValid($resume))
       {
            $response = $this->downloadDocuments($resume, $uploaderHelper); 
            return $response; 
       }
    }

    /**
     * @Route("/idcard/{id}/download", name="student_download_idcard", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function downloadIdCard(IdCard $idcard, UploaderHelper $uploaderHelper, UserChecker $checker)
    {
        if($checker->documentValid($idcard))
       {
            $response = $this->downloadDocuments($idcard, $uploaderHelper); 
            return $response;
       }
    }

     /**
     * @Route("/studentcard/{id}/download", name="student_download_studentcard", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function downloadStudentCard(StudentCard $studentcard, UploaderHelper $uploaderHelper, UserChecker $checker)
    {
        if($checker->documentValid($studentcard))
        {
            $response = $this->downloadDocuments($studentcard, $uploaderHelper); 
            return $response;
        }
    }

     /**
     * @Route("/proofhabitation/{id}/download", name="student_download_proofhabitation", methods={"GET"})
     * @IsGranted("ROLE_STUDENT")
     */
    public function downloadProofHabitation(ProofHabitation $proofHabitation, UploaderHelper $uploaderHelper, UserChecker $checker)
    {
        if($checker->documentValid($proofHabitation))
        {
            $response = $this->downloadDocuments($proofHabitation, $uploaderHelper); 
            return $response;
        }
    }
}
