<?php


namespace App\Services;


use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class UploadingService
{
    /**
     * Uploads a file in the "Documents" folder
     *
     * @param $formFile
     * @param string $targetDirectory
     * @return string
     */
    public function upload($formFile, string $targetDirectory)
    {
        $originalFilename = pathinfo($formFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $originalFilename . '-' . uniqid() . '.' . $formFile->guessExtension();
        $formFile->move(
            $targetDirectory,
            $newFilename
        );

        return $newFilename;
    }
}