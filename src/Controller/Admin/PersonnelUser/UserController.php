<?php

namespace App\Controller\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\User;
use App\Form\Admin\PersonnelUser\UserType;
use App\Form\Admin\PersonnelUser\UserRolesType;
use App\Repository\Admin\PersonnelUser\UserRepository;
use App\Repository\Admin\PersonnelUser\UserAccessRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="admin_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/personnel_user/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur ajouté avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/personnel_user/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            // Si c'est une requête Turbo Frame, rediriger vers la page index
            if ($request->headers->get('Turbo-Frame')) {
                return $this->redirectToRoute('admin_user_index');
            }

            return $this->redirectToRoute('admin_user_index');
        }

        // Si c'est une requête Turbo Frame, retourner seulement le formulaire
        if ($request->headers->get('Turbo-Frame')) {
            return $this->render('admin/personnel_user/user/_edit_form.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
            ]);
        }

        return $this->render('admin/personnel_user/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        }
        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/detail/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('admin/personnel_user/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/roles/edit", name="admin_user_roles_edit", methods={"GET", "POST"})
     */
    public function editRoles(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserRolesType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Rôles de l\'utilisateur mis à jour avec succès.');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/personnel_user/user/edit_roles.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/access", name="admin_user_manage_access", methods={"GET"})
     */
    public function manageAccess(User $user, UserAccessRepository $userAccessRepository): Response
    {
        return $this->render('admin/personnel_user/user/manage_access.html.twig', [
            'user' => $user,
            'user_accesses' => $userAccessRepository->findBy(['users' => $user]),
        ]);
    }

    /**
     * @Route("/{id}/roles/update-ajax", name="admin_user_roles_update_ajax", methods={"POST"})
     */
    public function updateRolesAjax(Request $request, User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // Or a more specific role

        $rolesString = $request->request->get('roles');
        $roles = array_map('trim', explode(',', $rolesString));
        $roles = array_filter($roles); // Remove empty roles

        $user->setRoles($roles);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'roles' => $user->getRoles(),
            'message' => 'Rôles mis à jour avec succès.'
        ]);
    }
}
