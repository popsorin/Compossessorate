<?php

namespace App\Controller;

use App\Entity\Document;
use App\FormType\DocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class DocumentController extends AbstractController
{
    /**
     * @Route("/documents", name="documents_list", methods={"GET"})
     */
    public function listDocuments()
    {
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAll();
        $form = $this->createForm(DocumentType::class, new Document());
        return $this->render("documents.html.twig", ["form" => $form->createView(), "documents" => $documents]);
    }

    /**
     * @Route("/documents", name="document_upload", methods={"POST"})
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function upload(Request $request, SluggerInterface $slugger)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('compossessorate_table')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('documents_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
        }
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAll();

        return $this->render("documents.html.twig", ["form" => $form->createView(), "documents" => $documents]);
//        $row = 1;
//        $file = $_FILES["document"];
//        if (($handle = fopen($file, "r")) !== FALSE) {
//            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//                $num = count($data);
//                echo "<p> $num fields in line $row: <br /></p>\n";
//                $row++;
//                for ($c=0; $c < $num; $c++) {
//                    echo $data[$c] . "<br />\n";
//                }
//            }
//            fclose($handle);
//        }
    }
}