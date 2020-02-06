<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
// use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Asset\Context\RequestStackContext;
use League\Flysystem\FileNotFoundException;


class UploaderHelper
{
    private $uploadsPath;
    private $filesystem;
    private $publicAssetBaseUrl; 
    // private $privateFilesystem; #

    // const PUBLIC_IMAGE = 'public_image'; 
    const STUDENT_IMAGE = 'student_image';
    // const STUDENT_REFERENCE = 'student_reference';

    public function __construct(FilesystemInterface $publicUploadsFilesystem, RequestStackContext $requestStackContext, LoggerInterface $logger, string $uploadedAssetsBaseUrl)
    {
        $this->filesystem = $publicUploadsFilesystem;
        // $this->privateFilesystem = $privateUploadsFilesystem; #
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
        $destination = $this->uploadsPath.'/'. self::STUDENT_IMAGE;
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $stream = fopen($uploadedFile->getPathname(), 'r');
        
        $result = $this->filesystem->writeStream(
            self::STUDENT_IMAGE.'/'.$newFilename,
            $stream
        );

        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }

        // $newFilename = $this->uploadFiles($file, self::PUBLIC_IMAGE, true); #

        if ($existingFilename) {
            try {
                $this->filesystem->delete($existingFilename);

                if ($result === false) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFilename));
                }

            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFilename));
            }
        }

        return $newFilename;
    }

    ## 
    // public function uploadPrivateFiles(UploadedFile $file): string
    // {
    //     return $this->uploadFiles($file, self::STUDENT_IMAGE, false);
    // }

    // private function uploadFiles(UploadedFile $file, string $directory, bool $isPublic): string
    // {
    //     if ($file instanceof UploadedFile) {
    //         $originalFilename = $file->getClientOriginalName();
    //     } else {
    //         $originalFilename = $file->getFilename();
    //     }
    //     $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$file->guessExtension();
        
    //     $stream = fopen($file->getPathname(), 'r');

    //     $filesystem = $isPublic ? $this->filesystem : $this->privateFilesystem;

    //     $result = $filesystem->writeStream(
    //         $directory.'/'.$newFilename,
    //         $stream
    //     );

    //     if ($result === false) {
    //         throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
    //     }

    //     if (is_resource($stream)) {
    //         fclose($stream);
    //     }

    //     return $newFilename;
    // }

}