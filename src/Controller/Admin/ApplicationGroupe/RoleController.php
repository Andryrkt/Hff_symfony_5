<?php

namespace App\Controller\Admin\ApplicationGroupe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/application-groupe/role")
 */
class RoleController extends AbstractController
{
    /**
     * @Route("/", name="app_admin_application_groupe_role_index", methods={"GET"})
     */
    public function index(): Response
    {
        // Définition des rôles disponibles dans l'application
        $roles = [
            [
                'code' => 'ROLE_USER',
                'label' => 'Utilisateur',
                'description' => 'Rôle de base pour tous les utilisateurs connectés',
                'level' => 1,
            ],
            [
                'code' => 'ROLE_ADMIN',
                'label' => 'Administrateur',
                'description' => 'Rôle avec accès complet à toutes les fonctionnalités',
                'level' => 10,
            ],
        ];

        return $this->render('admin/application_groupe/role/index.html.twig', [
            'roles' => $roles,
        ]);
    }

    /**
     * @Route("/{code}", name="app_admin_application_groupe_role_show", methods={"GET"})
     */
    public function show(string $code): Response
    {
        // Définition des rôles avec leurs détails
        $rolesData = [
            'ROLE_USER' => [
                'code' => 'ROLE_USER',
                'label' => 'Utilisateur',
                'description' => 'Rôle de base pour tous les utilisateurs connectés',
                'level' => 1,
                'permissions' => [
                    'Accès à l\'interface utilisateur',
                    'Consultation des données personnelles',
                    'Création de demandes d\'ordre de mission',
                ],
            ],
            'ROLE_ADMIN' => [
                'code' => 'ROLE_ADMIN',
                'label' => 'Administrateur',
                'description' => 'Rôle avec accès complet à toutes les fonctionnalités',
                'level' => 10,
                'permissions' => [
                    'Toutes les permissions de ROLE_USER',
                    'Gestion des utilisateurs',
                    'Gestion des permissions',
                    'Gestion des rôles',
                    'Accès aux fonctionnalités d\'administration',
                    'Validation des demandes',
                ],
            ],
        ];

        if (!isset($rolesData[$code])) {
            throw $this->createNotFoundException('Le rôle demandé n\'existe pas.');
        }

        return $this->render('admin/application_groupe/role/show.html.twig', [
            'role' => $rolesData[$code],
        ]);
    }
}
