<?php

namespace App\Controller\Api;

use App\Entity\Admin\AgenceService\Service;
use App\Repository\Admin\AgenceService\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/services", name="api_services_")
 */
class ServiceApiController extends AbstractController
{
    private $serviceRepository;
    private $entityManager;
    private $serializer;
    private $validator;

    public function __construct(
        ServiceRepository $serviceRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="list", methods={"GET"})
     */
    public function index(): JsonResponse
    {
        $services = $this->serviceRepository->findAll();

        $data = $this->serializer->serialize($services, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Service $service): JsonResponse
    {
        $data = $this->serializer->serialize($service, 'json', [
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

        $service = new Service();
        $service->setCode($data['code'] ?? '');
        $service->setNom($data['nom'] ?? '');

        $errors = $this->validator->validate($service);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        $responseData = $this->serializer->serialize($service, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['code'])) {
            $service->setCode($data['code']);
        }
        if (isset($data['nom'])) {
            $service->setNom($data['nom']);
        }

        $errors = $this->validator->validate($service);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $responseData = $this->serializer->serialize($service, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Service $service): JsonResponse
    {
        $this->entityManager->remove($service);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Service supprimé avec succès'], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/agences", name="agences", methods={"GET"})
     */
    public function getAgences(Service $service): JsonResponse
    {
        $agences = $service->getAgences();

        $data = $this->serializer->serialize($agences, 'json', [
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
        $services = $this->serviceRepository->searchByQuery($query);

        $data = $this->serializer->serialize($services, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
