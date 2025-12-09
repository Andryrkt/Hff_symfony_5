<?php

namespace App\Controller\Admin\Historisation;

use App\Entity\Admin\Historisation\TypeDocument;
use App\Form\Admin\Historisation\TypeDocumentType;
use App\Repository\Admin\Historisation\TypeDocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/historisation/type/document")
 */
class TypeDocumentController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_historisation_type_document_index", methods={"GET"})
     */
    public function index(TypeDocumentRepository $typeDocumentRepository): Response
    {
        return $this->render('admin/historisation/type_document/index.html.twig', [
            'type_documents' => $typeDocumentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_admin_historisation_type_document_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TypeDocumentRepository $typeDocumentRepository): Response
    {
        $typeDocument = new TypeDocument();
        $form = $this->createForm(TypeDocumentType::class, $typeDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeDocumentRepository->add($typeDocument, true);

            return $this->redirectToRoute('app_admin_historisation_type_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/historisation/type_document/new.html.twig', [
            'type_document' => $typeDocument,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_historisation_type_document_show", methods={"GET"})
     */
    public function show(TypeDocument $typeDocument): Response
    {
        return $this->render('admin/historisation/type_document/show.html.twig', [
            'type_document' => $typeDocument,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_historisation_type_document_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TypeDocument $typeDocument, TypeDocumentRepository $typeDocumentRepository): Response
    {
        $form = $this->createForm(TypeDocumentType::class, $typeDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeDocumentRepository->add($typeDocument, true);

            return $this->redirectToRoute('app_admin_historisation_type_document_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/historisation/type_document/edit.html.twig', [
            'type_document' => $typeDocument,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_historisation_type_document_delete", methods={"POST"})
     */
    public function delete(Request $request, TypeDocument $typeDocument, TypeDocumentRepository $typeDocumentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typeDocument->getId(), $request->request->get('_token'))) {
            $typeDocumentRepository->remove($typeDocument, true);
        }

        return $this->redirectToRoute('app_admin_historisation_type_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
