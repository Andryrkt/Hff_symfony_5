<?php

namespace App\Controller\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Form\Admin\PersonnelUser\PersonnelType;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/personnel/user/personnel")
 */
class PersonnelController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_personnel_user_personnel_index", methods={"GET"})
     */
    public function index(PersonnelRepository $personnelRepository): Response
    {
        return $this->render('admin/personnel_user/personnel/index.html.twig', [
            'personnels' => $personnelRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_admin_personnel_user_personnel_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PersonnelRepository $personnelRepository): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personnelRepository->add($personnel, true);

            return $this->redirectToRoute('app_admin_personnel_user_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/personnel_user/personnel/new.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_personnel_user_personnel_show", methods={"GET"})
     */
    public function show(Personnel $personnel): Response
    {
        return $this->render('admin/personnel_user/personnel/show.html.twig', [
            'personnel' => $personnel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_admin_personnel_user_personnel_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Personnel $personnel, PersonnelRepository $personnelRepository): Response
    {
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personnelRepository->add($personnel, true);

            return $this->redirectToRoute('app_admin_personnel_user_personnel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/personnel_user/personnel/edit.html.twig', [
            'personnel' => $personnel,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_admin_personnel_user_personnel_delete", methods={"POST"})
     */
    public function delete(Request $request, Personnel $personnel, PersonnelRepository $personnelRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personnel->getId(), $request->request->get('_token'))) {
            $personnelRepository->remove($personnel, true);
        }

        return $this->redirectToRoute('app_admin_personnel_user_personnel_index', [], Response::HTTP_SEE_OTHER);
    }
}
