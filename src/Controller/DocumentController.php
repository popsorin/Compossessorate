<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Task;
use App\FormType\DocumentType;
use App\FormType\FormDocumentType;
use App\Repository\DocumentRepository;
use App\Services\DocumentFactory;
use Doctrine\DBAL\ConnectionException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\ORMException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentController extends AbstractController
{
    /**
     * @Route("/documents", name="documents_list", methods={"GET"})
     */
    public function listDocuments()
    {
        $documents = $this->getDoctrine()->getRepository(Document::class)->findAll();
        $form = $this->createForm(DocumentType::class, new Document());
        return $this->render("compossessorate/documents.html.twig", ["form" => $form->createView(), "documents" => $documents]);
    }

    /**
     * @Route("/documents", name="document_upload", methods={"POST"})
     *
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param DocumentFactory $documentFactory
     * @return Response
     * @throws ReflectionException|ConnectionException
     */
    public function upload(Request $request, SluggerInterface $slugger, DocumentFactory $documentFactory)
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
            /** @var DocumentRepository $documentRepository */
            $documentRepository = $this->getDoctrine()->getRepository(Document::class);
            $documents = $documentFactory->createFromFile(sprintf("%s/%s",$this->getParameter('documents_directory'), $newFilename));
            try {
                $documents = $documentRepository->insertMultipleValues($documents);
            } catch (ORMException $exception) {
                return $this->render("error.html.twig", ["form" => $form->createView(), "documents" => $exception->getMessage()]);
            }
            $documents = $documentRepository->findAll();
        }

        return $this->render("compossessorate/documents.html.twig", ["form" => $form->createView(), "documents" => $documents]);
    }

    /**
     * @Route("/document/new", name="document_new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return string
     */
    public function new(Request $request, ValidatorInterface $validator)
    {
        $document = new Document();

        $form = $this->createForm(FormDocumentType::class, $document);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $document = $form->getData();
            $errors = $validator->validate($document);
            if(count($errors) > 0) {
                return $this->render("compossessorate/new-document.html.twig", [
                    "errors" => $errors]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($document);
            $entityManager->flush();

            return $this->redirect('/documents');
        }

        return $this->render('compossessorate/new-document.html.twig', ['form' => $form->createView(), 'errors' => []]);
    }
}