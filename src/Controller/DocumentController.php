<?php

namespace App\Controller;

use App\Entity\Document;
use App\FormType\UploadDocumentType;
use App\FormType\FormDocumentType;
use App\Repository\DocumentRepository;
use App\Services\PaginatorService;
use App\Services\PDFConverter;
use App\Services\DocumentFactory;
use App\Services\RequestParameterBag;
use App\Services\UploadingService;
use App\Services\URLBuilder;
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
     * @Route("/documents", name="documents_list", methods={"GET", "POST"})
     * @param Request $request
     * @param RequestParameterBag $parameterBag
     * @param URLBuilder $URLBuilder
     * @return Response
     */
    public function listDocuments(
        Request $request,
        RequestParameterBag $parameterBag,
        URLBuilder $URLBuilder
    ): Response {
        $parameterBag->createFromRequest($request);
        $documentRepository = $this->getDoctrine()->getRepository(Document::class);
        $paginator = new PaginatorService(
            $parameterBag->get("documents_per_page"),
            count($documentRepository->findAll()),
            $parameterBag->get("page")
        );
        $documents = $documentRepository->findBy(
            [],
            $parameterBag->getArray("fullname"),
            $paginator->getDocumentsPerPage(),
            $paginator->getOffset()
        );
        $uploadForm = $this->createForm(UploadDocumentType::class, new Document());

        return $this->render("compossessorate/documents.html.twig", [
            "orderURL" => $URLBuilder->get($parameterBag,"fullname"),
            "documentsPerPageURL" => $URLBuilder->get($parameterBag,"documents_per_page"),
            "pageURL" => $URLBuilder->get($parameterBag, "page"),
            "page" => $paginator->getCurrentPage(),
            "previousPage" => $paginator->getPreviousPage(),
            "nextPage" => $paginator->getNextPage(),
            "uploadForm" => $uploadForm->createView(),
            "documents" => $documents
        ]);
    }

    /**
     * @Route("/document/upload", name="document_upload", methods={"POST"})
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
        $form = $this->createForm(UploadDocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('compossessorate_table')->getData();
            $documentDirectory = $this->getParameter('documents_directory');
            if($brochureFile){
                $newFilename = $service->upload($brochureFile, $documentDirectory);
            }
            /** @var DocumentRepository $documentRepository */
            $documentRepository = $this->getDoctrine()->getRepository(Document::class);
            $documents = $documentFactory->createFromFile(sprintf("%s/%s",$this->getParameter('documents_directory'), $newFilename));
            try {
                $documentRepository->insertMultipleValues($documents);
                unlink(sprintf('%s/%s', $documentDirectory, $newFilename));
            } catch (ORMException $exception) {
                return $this->render("compossessorate/documents.html.twig", [
                    "form" => $form->createView(),
                    "documents" => $exception->getMessage()
                ]);
            } catch (FileException $exception) {
                return $this->render("error.html.twig", [
                    "form" => $form->createView(),
                    "documents" => $exception->getMessage()
                ]);
            }
        }

        return $this->redirect("/documents");
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

        return $this->render('compossessorate/new-document.html.twig', [
            'form' => $form->createView(), 'errors' => []
        ]);
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
                return $this->render("compossessorate/new-document.html.twig", [
                    'form' => $form->createView(),
                    "errors" => $errors
                ]);
            }
            $entityManager->flush();

            return $this->redirect('/documents');
        }

        return $this->render('compossessorate/new-document.html.twig', [
            'form' => $form->createView(),
            'errors' => []
        ]);
    }

    /**
     * @Route("/documents/convert", name="documents_convert", methods={"GET"})
     * @param Request $request
     * @param PDFConverter $converter
     * @param RequestParameterBag $parameterBag
     * @return RedirectResponse
     */
    public function convert(
        Request $request,
        PDFConverter $converter,
        RequestParameterBag $parameterBag,
        URLBuilder $URLBuilder
    ) {
        $parameterBag->createFromRequest($request);
        $documentRepository = $this->getDoctrine()->getRepository(Document::class);
        $paginator = new PaginatorService(
            $parameterBag->get("documents_per_page"),
            count($documentRepository->findAll()),
            $parameterBag->get("page")
        );
        $document = new Document();
        $documents =  $documentRepository->findBy(
            [],
            $parameterBag->getArray("fullname"),
            $paginator->getDocumentsPerPage(),
            $paginator->getOffset()
        );
        $form = $this->createForm(UploadDocumentType::class, $document);
        $html = $this->renderView("compossessorate/pdf-table-of-documents.html.twig", [
            "documents" => $documents,
            'form' => $form->createView()
        ]);
        $projectDirectory = $this->getParameter("kernel.project_dir");
        $converter->convert($html, $paginator->getCurrentPage(), $projectDirectory);

        return $this->redirect(sprintf("/documents?%s", $URLBuilder->get($parameterBag)));
    }
}