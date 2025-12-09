<?php

namespace App\Controller\Admin\Historisation;

use App\Entity\Admin\Historisation\TypeOperation;
use App\Form\Admin\TypeOperationType;
use App\Repository\Admin\Historisation\TypeOperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/type/operation")
 */
class TypeOperationController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_type_operation_index", methods={"GET"})
     */
    public function index(TypeOperationRepository $typeOperationRepository): Response
    {
        return $this->render('admin/historisation/type_operation/index.html.twig', [
            'type_operations' => $typeOperationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_admin_type_operation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TypeOperationRepository $typeOperationRepository): Response
    {
        $typeOperation = new TypeOperation();
        $form = $this->createForm(TypeOperationType::class, $typeOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeOperationRepository->add($typeOperation, true);

            return $this->redirectToRoute('app_admin_type_operation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/historisation/type_operation/new.html.twig', [
            'type_operation' => $typeOperation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_type_operation_show", methods={"GET"})
     */
    public function show(TypeOperation $typeOperation): Response
    {
        return $this->render('admin/historisation/type_operation/show.html.twig', [
            'type_operation' => $typeOperation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_type_operation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TypeOperation $typeOperation, TypeOperationRepository $typeOperationRepository): Response
    {
        $form = $this->createForm(TypeOperationType::class, $typeOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typeOperationRepository->add($typeOperation, true);

            return $this->redirectToRoute('app_admin_type_operation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/historisation/type_operation/edit.html.twig', [
            'type_operation' => $typeOperation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_type_operation_delete", methods={"POST"})
     */
    public function delete(Request $request, TypeOperation $typeOperation, TypeOperationRepository $typeOperationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $typeOperation->getId(), $request->request->get('_token'))) {
            $typeOperationRepository->remove($typeOperation, true);
        }

        return $this->redirectToRoute('app_admin_type_operation_index', [], Response::HTTP_SEE_OTHER);
    }
}
