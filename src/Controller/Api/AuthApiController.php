<?php

namespace App\Controller\Api;

use App\Service\LdapService;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Group;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\Admin\PersonnelUser\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/api/auth", name="api_auth_")
 */
class AuthApiController extends AbstractController
{
    private $ldapService;
    private $userRepository;
    private $tokenStorage;
    private $serializer;

    public function __construct(
        LdapService $ldapService,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        SerializerInterface $serializer
    ) {
        $this->ldapService = $ldapService;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/me", name="me", methods={"GET"})
     */
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => 'Utilisateur non authentifié'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $userData = $this->serializer->serialize($user, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse(json_decode($userData, true), Response::HTTP_OK);
    }

    /**
     * @Route("/permissions", name="permissions", methods={"GET"})
     */
    public function getPermissions(): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => 'Utilisateur non authentifié'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $permissions = [
            'roles' => $user->getRoles(),
            'userAccesses' => [],
            'groups' => []
        ];

        // Récupérer les accès utilisateur
        foreach ($user->getUserAccesses() as $userAccess) {
            $permissions['userAccesses'][] = [
                'id' => $userAccess->getId(),
                'agence' => $userAccess->getAgence() ? [
                    'id' => $userAccess->getAgence()->getId(),
                    'code' => $userAccess->getAgence()->getCode(),
                    'nom' => $userAccess->getAgence()->getNom()
                ] : null,
                'service' => $userAccess->getService() ? [
                    'id' => $userAccess->getService()->getId(),
                    'code' => $userAccess->getService()->getCode(),
                    'nom' => $userAccess->getService()->getNom()
                ] : null
            ];
        }

        // Récupérer les groupes
        foreach ($user->getGroups() as $group) {
            $permissions['groups'][] = [
                'id' => $group->getId(),
                'nom' => $group->getName()
            ];
        }

        return new JsonResponse($permissions, Response::HTTP_OK);
    }

    /**
     * @Route("/check-access", name="check_access", methods={"POST"})
     */
    public function checkAccess(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([
                'error' => 'Utilisateur non authentifié'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $agenceCode = $data['agence_code'] ?? null;
        $serviceCode = $data['service_code'] ?? null;

        $hasAccess = false;

        // Vérifier les accès utilisateur
        foreach ($user->getUserAccesses() as $userAccess) {
            $agence = $userAccess->getAgence();
            $service = $userAccess->getService();

            if ($agenceCode && $agence && $agence->getCode() === $agenceCode) {
                if (!$serviceCode || ($service && $service->getCode() === $serviceCode)) {
                    $hasAccess = true;
                    break;
                }
            }
        }

        return new JsonResponse([
            'has_access' => $hasAccess,
            'user_id' => $user->getId(),
            'agence_code' => $agenceCode,
            'service_code' => $serviceCode
        ], Response::HTTP_OK);
    }
}
