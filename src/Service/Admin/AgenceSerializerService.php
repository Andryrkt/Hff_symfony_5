<?php

namespace App\Service\Admin;

use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Service de sérialisation des agences
 * 
 * Ce service fournit des méthodes pour sérialiser les données des agences
 * dans différents formats (JSON, etc.) avec des groupes de sérialisation spécifiques.
 */
class AgenceSerializerService
{
    private AgenceRepository $agenceRepository;
    private SerializerInterface $serializer;

    public function __construct(
        AgenceRepository $agenceRepository,
        SerializerInterface $serializer
    ) {
        $this->agenceRepository = $agenceRepository;
        $this->serializer = $serializer;
    }

    /**
     * Sérialise toutes les agences au format JSON avec le groupe 'agence:read'
     *
     * @return string JSON des agences
     */
    public function serializeAllAgences(): string
    {
        $agences = $this->agenceRepository->findAll();
        return $this->serializer->serialize($agences, 'json', ['groups' => 'agence:read']);
    }

    /**
     * Sérialise toutes les agences au format JSON avec des groupes de sérialisation personnalisés
     *
     * @param array $groups Groupes de sérialisation à utiliser
     * @return string JSON des agences
     */
    public function serializeAllAgencesWithGroups(array $groups): string
    {
        $agences = $this->agenceRepository->findAll();
        return $this->serializer->serialize($agences, 'json', ['groups' => $groups]);
    }

    /**
     * Sérialise une collection d'agences au format JSON
     *
     * @param array $agences Collection d'agences à sérialiser
     * @param array $groups Groupes de sérialisation à utiliser
     * @return string JSON des agences
     */
    public function serializeAgences(array $agences, array $groups = ['agence:read']): string
    {
        return $this->serializer->serialize($agences, 'json', ['groups' => $groups]);
    }
}
