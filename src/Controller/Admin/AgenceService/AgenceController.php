<?php

namespace App\Controller\Admin\AgenceService;

use App\Entity\Admin\AgenceService\Agence;
use App\Form\Admin\AgenceService\AgenceType;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/agence")
 */
class AgenceController extends AbstractController
{
    /**
     * @Route("/", name="admin_agence_index", methods={"GET"})
     */
    public function index(AgenceRepository $agenceRepository): Response
    {
        return $this->render('admin/agenceService/agence/index.html.twig', [
            'agences' => $agenceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_agence_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgenceRepository $agenceRepository): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceRepository->add($agence, true);

            return $this->redirectToRoute('admin_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenceService/agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_agence_show", methods={"GET"})
     */
    public function show(Agence $agence): Response
    {
        return $this->render('admin/agenceService/agence/show.html.twig', [
            'agence' => $agence,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_agence_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Agence $agence, AgenceRepository $agenceRepository): Response
    {
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agenceRepository->add($agence, true);

            return $this->redirectToRoute('admin_agence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/agenceService/agence/edit.html.twig', [
            'agence' => $agence,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_agence_delete", methods={"POST"})
     */
    public function delete(Request $request, Agence $agence, AgenceRepository $agenceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $agence->getId(), $request->request->get('_token'))) {
            $agenceRepository->remove($agence, true);
        }

        return $this->redirectToRoute('admin_agence_index', [], Response::HTTP_SEE_OTHER);
    }
}
