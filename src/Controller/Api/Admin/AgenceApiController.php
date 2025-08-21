<?php

namespace App\Controller\Api\Admin;

use App\Entity\Admin\AgenceService\Agence;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/agences", name="api_agences_")
 */
class AgenceApiController extends AbstractController
{
    private $agenceRepository;
    private $entityManager;
    private $serializer;
    private $validator;

    public function __construct(
        AgenceRepository $agenceRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->agenceRepository = $agenceRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="list", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $agences = $this->agenceRepository->findAll();

        $data = $this->serializer->serialize($agences, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Agence $agence): JsonResponse
    {
        $data = $this->serializer->serialize($agence, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $agence = new Agence();
        $agence->setCode($data['code'] ?? '');
        $agence->setNom($data['nom'] ?? '');

        $errors = $this->validator->validate($agence);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($agence);
        $this->entityManager->flush();

        $responseData = $this->serializer->serialize($agence, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(Request $request, Agence $agence): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['code'])) {
            $agence->setCode($data['code']);
        }
        if (isset($data['nom'])) {
            $agence->setNom($data['nom']);
        }

        $errors = $this->validator->validate($agence);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $responseData = $this->serializer->serialize($agence, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Agence $agence): JsonResponse
    {
        $this->entityManager->remove($agence);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Agence supprimée avec succès'], Response::HTTP_OK);
    }

    // The GET /api/agences/{id}/services endpoint is now handled by API Platform
    // via the Agence entity's item operation "get_services".

    /**
     * @Route("/{id}/users", name="users", methods={"GET"})
     */
    public function getUsers(Agence $agence): JsonResponse
    {
        $userAccesses = $agence->getUserAccesses();
        $users = [];

        foreach ($userAccesses as $userAccess) {
            $users[] = $userAccess->getUsers();
        }

        $data = $this->serializer->serialize($users, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/search", name="search", methods={"GET"})
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        $agences = $this->agenceRepository->searchByQuery($query);

        $data = $this->serializer->serialize($agences, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
