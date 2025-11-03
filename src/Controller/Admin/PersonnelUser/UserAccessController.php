<?php

namespace App\Controller\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Form\Admin\PersonnelUser\UserAccessType;
use App\Repository\Admin\PersonnelUser\UserAccessRepository;
use App\Repository\Admin\PersonnelUser\UserRepository;
use App\Entity\Admin\PersonnelUser\User; // ADDED: Missing use statement for User entity
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user/access")
 */
class UserAccessController extends AbstractController
{
    /**
     * @Route("/", name="admin_user_access_index", methods={"GET"})
     */
    public function index(UserAccessRepository $userAccessRepository): Response
    {
        return $this->render('admin/personnel_user/userAccess/index.html.twig', [
            'user_accesses' => $userAccessRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_user_access_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserAccessRepository $userAccessRepository, UserRepository $userRepository): Response
    {
        $userAccess = new UserAccess();

        $userId = $request->query->get('user_id');
        if ($userId) {
            $user = $userRepository->find($userId);
            if ($user) {
                $userAccess->setUsers($user);
            }
        }

        $form = $this->createForm(UserAccessType::class, $userAccess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userAccessRepository->add($userAccess, true);

            return $this->redirectToRoute('admin_user_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/personnel_user/userAccess/new.html.twig', [
            'user_access' => $userAccess,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_access_show", methods={"GET"})
     */
    public function show(UserAccess $userAccess): Response
    {
        return $this->render('admin/personnel_user/userAccess/show.html.twig', [
            'user_access' => $userAccess,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_access_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UserAccess $userAccess, UserAccessRepository $userAccessRepository): Response
    {
        $form = $this->createForm(UserAccessType::class, $userAccess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userAccessRepository->add($userAccess, true);

            return $this->redirectToRoute('admin_user_access_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/personnel_user/userAccess/edit.html.twig', [
            'user_access' => $userAccess,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_access_delete", methods={"POST"})
     */
    public function delete(Request $request, UserAccess $userAccess, UserAccessRepository $userAccessRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userAccess->getId(), $request->request->get('_token'))) {
            $userAccessRepository->remove($userAccess, true);
        }

        return $this->redirectToRoute('admin_user_access_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/user/{id}", name="admin_user_access_for_user", methods={"GET"})
     */
    public function listForUser(User $user, UserAccessRepository $userAccessRepository): Response
    {
        return $this->render('admin/personnel_user/userAccess/_list_for_user.html.twig', [
            'user_accesses' => $userAccessRepository->findBy(['users' => $user]),
            'user' => $user,
        ]);
    }
}
