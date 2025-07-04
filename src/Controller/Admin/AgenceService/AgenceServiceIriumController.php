<?php

namespace App\Controller\Admin\AgenceService;

use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use App\Form\Admin\AgenceService\AgenceServiceType;
use App\Repository\Admin\AgenceService\AgenceServiceIriumRepository;
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
    public function index(AgenceServiceIriumRepository $agenceServiceIriumRepository): Response
    {
        return $this->render('admin/agenceService/agence_service/index.html.twig', [
            'agence_services' => $agenceServiceIriumRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_agence_service_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgenceServiceIriumRepository $agenceServiceIriumRepository): Response
    {
        $agenceServiceIrium = new AgenceServiceIrium();
        $form = $this->createForm(AgenceServiceType::class, $agenceServiceIrium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceServiceIriumRepository->add($agenceServiceIrium, true);

            return $this->redirectToRoute('admin_agence_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenceService/agence_service/new.html.twig', [
            'agence_service' => $agenceServiceIrium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_agence_service_show", methods={"GET"})
     */
    public function show(AgenceServiceIrium $agenceServiceIrium): Response
    {
        return $this->render('admin/agenceService/agence_service/show.html.twig', [
            'agence_service' => $agenceServiceIrium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_agence_service_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, AgenceServiceIrium $agenceServiceIrium, AgenceServiceIriumRepository $agenceServiceIriumRepository): Response
    {
        $form = $this->createForm(AgenceServiceType::class, $agenceServiceIrium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceServiceIriumRepository->add($agenceServiceIrium, true);

            return $this->redirectToRoute('admin_agence_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenceService/agence_service/edit.html.twig', [
            'agence_service' => $agenceServiceIrium,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_agence_service_delete", methods={"POST"})
     */
    public function delete(Request $request, AgenceServiceIrium $agenceServiceIrium, AgenceServiceIriumRepository $agenceServiceIriumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $agenceServiceIrium->getId(), $request->request->get('_token'))) {
            $agenceServiceIriumRepository->remove($agenceServiceIrium, true);
        }

        return $this->redirectToRoute('admin_agence_service_index', [], Response::HTTP_SEE_OTHER);
    }
}
