<?php

namespace App\Service;

use App\Entity\IdCard;
use App\Entity\Resume;
use App\Entity\Student;
use App\Entity\Pictures;
use App\Entity\StudentCard;
use App\Entity\ProofHabitation;
use App\Service\UploaderHelper;

class StudentHelper
{
    private $uploaderHelper;
    private $mailer;

    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }

    // new student
    public function uploadNew($form, $keys, $student)
    {     
        foreach($keys as $key) {
            // get file 
            $uploadedFile = $form[$key]['file']->getData();
            // get entity
            $entity = $form[$key]->getName();
            $get = 'get' . ucfirst($entity); 
            $set = 'set' . ucfirst($entity);
            $class = "App\Entity\\" . ucfirst($entity);

            if($uploadedFile) {
                // upload file 
                $newFilename = $this->uploaderHelper->uploadPrivateFile($uploadedFile, $student->$get()->getFileName());
                // update entity 
                $document = new $class;
                $document->setFileName($newFilename);
                $document->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                $document->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');                    
            } 
            // update relation
            $student->$set($document);
        }  
    }
    
    public function editStudentDocs($formDoc, $form, $student, $request)
    {   
        // update only not null files fields 
        $keys = array_keys($request->files->get('update_student_doc'));
        
        foreach($keys as $key) {

            $uploadedFile = $formDoc[$key]->getData();
            $entity = $formDoc[$key]->getName();
            // get files name
            switch($entity) {
                case 'resumes':
                    $entity = 'resume';
                    $document = $form->getData()->getResume();
                break;
                case 'idCards':
                    $entity = 'idCard';
                    $document = $form->getData()->getIdCard();
                break;
                case 'studentCards':
                    $entity = 'studentCard';
                    $document = $form->getData()->getStudentCard();
                break;
                case 'proofHabitations':
                    $entity = 'proofHabitation';
                    $document = $form->getData()->getProofHabitation();
                break;
            }
            // update document entity
            $get = 'get' . ucfirst($entity); 
            $set = 'set' . ucfirst($entity);
            $class = "App\Entity\\" . ucfirst($entity);

            if($uploadedFile) {
                $newFilename = $this->uploaderHelper->uploadPrivateFile($uploadedFile, $student->$get()->getFileName());
                
                $document->setFileName($newFilename);
                $document->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
                $document->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');                    
            } 
            // update relation 
            if($uploadedFile != null) {
                $student->$set($document);
            }
        } 
    }
}