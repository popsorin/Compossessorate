<?php


namespace App\Services;


use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class UploadingService
{
    private SluggerInterface $slugger;

    public function __construct( SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function upload($formFile, string $targetDirectory)
    {
        $originalFilename = pathinfo($formFile->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $formFile->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $formFile->move(
                $targetDirectory,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $newFilename;
    }
}