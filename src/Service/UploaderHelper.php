<?php

namespace App\Service;

use App\Entity\Pictures;
use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;


class UploaderHelper
{
    private $uploadsPath;
    private $filesystem;
    private $publicAssetBaseUrl; 
    private $privateFilesystem; #

    const STUDENT_DOCUMENT = 'student_document';
    const PROFILE_PICTURES = 'profile_pictures';

    public function __construct(FilesystemInterface $publicUploadsFilesystem, FilesystemInterface $privateUploadsFilesystem, RequestStackContext $requestStackContext, LoggerInterface $logger, string $uploadedAssetsBaseUrl)
    {
        $this->filesystem = $publicUploadsFilesystem;
        $this->privateFilesystem = $privateUploadsFilesystem; #
        $this->requestStackContext = $requestStackContext;
        $this->logger = $logger;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl; 
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
            ->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
    }

    // edit docs and picture
    public function uploadEdit($uploadedFile, $entity)
    {
        // upload file 
        if($uploadedFile) {
            if($entity->getPictures() != null) {
                $newFilename = $this->uploadFile($uploadedFile, $entity->getPictures()->getFileName());
            }
            else {
                $newFilename = $this->uploadFile($uploadedFile, null);
            }
            // set picture entity 
            $document = new Pictures;
            $document->setFileName($newFilename);
            $document->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $newFilename);
            $document->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream'); 
            $entity->setPictures($document);                   
        }
    }

    public function uploadFile(UploadedFile $uploadedFile, ?string $existingFilename): string
    {
        $newFilename = $this->uploads($uploadedFile, '', true);

        if ($existingFilename) {
            try {
                $this->filesystem->delete($existingFilename);

            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }

        return $newFilename;
    }

    public function uploadPrivateFile(UploadedFile $uploadedFile, ?string $existingFilename): string
    {
        $newFilename = $this->uploads($uploadedFile, self::STUDENT_DOCUMENT, false);

        // dd($existingFilename);

        if ($existingFilename) {

            try {
               $result = $this->privateFilesystem->delete($existingFilename);

            } catch (FileNotFoundException $e) {
                // throw new \Exception('pas possible');
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }

        return $newFilename;
    }

    private function uploads(UploadedFile $uploadedFile, string $directory, bool $isPublic)
    {
        if ($uploadedFile instanceof UploadedFile) {
            $originalFilename = $uploadedFile->getClientOriginalName();
        } else {
            $originalFilename = $uploadedFile->getFilename();
        }
        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $stream = fopen($uploadedFile->getPathname(), 'r');

        $filesystem = $isPublic ? $this->filesystem : $this->privateFilesystem;

        $result = $filesystem->writeStream(
            $directory.'/'.$newFilename,
            $stream
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }

        return $newFilename;
    }

    // delete file when delete entity 
    public function deleteFile($fileName)
    {
       try {      
            $result = $this->privateFilesystem->delete($fileName);

        } catch (FileNotFoundException $e) {
            $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $fileName));
        }
    }

    /**
     * @return resource
     */
    public function readStream(string $path, bool $isPublic)
    {
        $filesystem = $isPublic ? $this->filesystem : $this->privateFilesystem;
        $resource = $filesystem->readStream($path);
        if ($resource === false) {
            throw new \Exception(sprintf('Error opening stream for "%s"', $path));
        }
        return $resource;
    }

}