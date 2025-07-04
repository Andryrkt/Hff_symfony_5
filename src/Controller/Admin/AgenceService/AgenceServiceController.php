<?php

namespace App\Controller\Admin\AgenceService;

use App\Entity\AgenceService;
use App\Form\AgenceServiceType;
use App\Repository\AgenceServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/agence/service")
 */
class AgenceServiceController extends AbstractController
{
    /**
     * @Route("/", name="admin_agence_service_index", methods={"GET"})
     */
    public function index(AgenceServiceRepository $agenceServiceRepository): Response
    {
        return $this->render('admin/agenceService/agenceService/index.html.twig', [
            'agence_services' => $agenceServiceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_agence_service_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgenceServiceRepository $agenceServiceRepository): Response
    {
        $agenceService = new AgenceService();
        $form = $this->createForm(AgenceServiceType::class, $agenceService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceServiceRepository->add($agenceService, true);

            return $this->redirectToRoute('admin_agence_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenceService/agenceService/new.html.twig', [
            'agence_service' => $agenceService,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_agence_service_show", methods={"GET"})
     */
    public function show(AgenceService $agenceService): Response
    {
        return $this->render('admin/agenceService/agenceService/show.html.twig', [
            'agence_service' => $agenceService,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_agence_service_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, AgenceService $agenceService, AgenceServiceRepository $agenceServiceRepository): Response
    {
        $form = $this->createForm(AgenceServiceType::class, $agenceService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceServiceRepository->add($agenceService, true);

            return $this->redirectToRoute('admin_agence_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenceService/agenceService/edit.html.twig', [
            'agence_service' => $agenceService,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_agence_service_delete", methods={"POST"})
     */
    public function delete(Request $request, AgenceService $agenceService, AgenceServiceRepository $agenceServiceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agenceService->getId(), $request->request->get('_token'))) {
            $agenceServiceRepository->remove($agenceService, true);
        }

        return $this->redirectToRoute('admin_agence_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
