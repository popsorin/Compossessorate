<?php

namespace App\Controller;

use App\Entity\Document;
use App\FormType\DocumentType;
use App\FormType\FormDocumentType;
use App\Repository\DocumentRepository;
use App\Services\DocumentFactory;
use App\Services\RequestParameterBag;
use App\Services\UploadingService;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DocumentController extends AbstractController
{
    /**
     * @Route("/documents", name="documents_list", methods={"GET"})
     * @param Request $request
     * @param RequestParameterBag $parameterBag
     * @return Response
     */
    public function listDocuments(Request $request, RequestParameterBag $parameterBag)
    {
        $order = $parameterBag->createFromRequest($request);
        $documents = $this->getDoctrine()->getRepository(Document::class)->findBy([], $order);
        $form = $this->createForm(DocumentType::class, new Document());
        return $this->render("compossessorate/documents.html.twig", ["form" => $form->createView(), "documents" => $documents]);
    }

    /**
     * @Route("/documents", name="document_upload", methods={"POST"})
     *
     * @param Request $request
     * @param UploadingService $service
     * @param DocumentFactory $documentFactory
     * @return Response
     * @throws ConnectionException
     * @throws ReflectionException
     */
    public function upload(Request $request, UploadingService $service, DocumentFactory $documentFactory)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('compossessorate_table')->getData();
            if($brochureFile){
                $newFilename = $service->upload($brochureFile, $this->getParameter('documents_directory'));
            }
            /** @var DocumentRepository $documentRepository */
            $documentRepository = $this->getDoctrine()->getRepository(Document::class);
            $documents = $documentFactory->createFromFile(sprintf("%s/%s",$this->getParameter('documents_directory'), $newFilename));
            try {
                $documentRepository->insertMultipleValues($documents);
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
     * @param ValidatorInterface $validator
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

    /**
     * @Route("/document/delete/{id}", name="document_delete", methods={"GET"})
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        $document = $entityManager->getRepository(Document::class)->find($id);
        $entityManager->remove($document);
        $entityManager->flush();

        return $this->redirect('/documents');
    }

    /**
     * @Route("/document/update/{id}", name="document_update", methods={"GET", "POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return RedirectResponse|Response
     */
    public function update(Request $request, ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $document = $entityManager->getRepository(Document::class)->find($request->get("id"));
        $form = $this->createForm(FormDocumentType::class, $document);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $document = $form->getData();
            $errors = $validator->validate($document);
            if(count($errors) > 0) {
                return $this->render("compossessorate/new-document.html.twig", ['form' => $form->createView(),
                    "errors" => $errors]);
            }
            $entityManager->flush();

            return $this->redirect('/documents');
        }

        return $this->render('compossessorate/new-document.html.twig', ['form' => $form->createView(), 'errors' => []]);
    }
}