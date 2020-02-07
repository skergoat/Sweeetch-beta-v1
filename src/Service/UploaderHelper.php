<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;
use League\Flysystem\FileNotFoundException;


class UploaderHelper
{
    private $uploadsPath;
    private $filesystem;
    private $publicAssetBaseUrl; 
    private $privateFilesystem; #

    const STUDENT_IMAGE = 'student_image';
    const STUDENT_DOCUMENT = 'student_document';

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

    public function uploadFile(UploadedFile $uploadedFile, ?string $existingFilename): string
    {
        // $destination = $this->uploadsPath.'/'. self::STUDENT_IMAGE;
        // $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        // $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        // $stream = fopen($uploadedFile->getPathname(), 'r');
        
        // $result = $this->filesystem->writeStream(
        //     self::STUDENT_IMAGE.'/'.$newFilename,
        //     $stream
        // );

        // if (is_resource($stream)) {
        //     fclose($stream);
        // }

        // if ($result === false) {
        //     throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        // }

        $newFilename = $this->uploads($uploadedFile, self::STUDENT_IMAGE, true);

        // if ($existingFilename) {
        //     try {
        //         $this->filesystem->delete($existingFilename);

        //         if ($result === false) {
        //             throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
        //         }

        //     } catch (FileNotFoundException $e) {
        //         $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
        //     }
        // }

        return $newFilename;
    }

    public function uploadPrivateFile(UploadedFile $uploadedFile, ?string $existingFilename): string
    {
        return $this->uploads($uploadedFile, self::STUDENT_DOCUMENT, false);

        // if ($existingFilename) {
        //     try {
        //         $this->filesystem->delete($existingFilename);

        //         if ($result === false) {
        //             throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
        //         }

        //     } catch (FileNotFoundException $e) {
        //         $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
        //     }
        // }

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