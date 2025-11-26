<?php

namespace App\Service\Admin;

use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;

/**
 * Service de sérialisation des agences
 * 
 * Ce service fournit des méthodes pour sérialiser les données des agences
 * dans différents formats (JSON, etc.) avec des groupes de sérialisation spécifiques.
 * Utilise un système de cache pour optimiser les performances.
 */
class AgenceSerializerService
{
    private AgenceRepository $agenceRepository;
    private SerializerInterface $serializer;
    private CacheInterface $cache;
    private LoggerInterface $logger;

    // Durée du cache : 1 heure (les agences changent rarement)
    private const CACHE_TTL = 3600;
    private const CACHE_KEY_DROPDOWN = 'agences_dropdown_optimized';
    private const CACHE_KEY_ALL = 'agences_all';

    public function __construct(
        AgenceRepository $agenceRepository,
        SerializerInterface $serializer,
        CacheInterface $cache,
        LoggerInterface $logger
    ) {
        $this->agenceRepository = $agenceRepository;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * Sérialise toutes les agences au format JSON avec le groupe 'agence:read'
     *
     * @return string JSON des agences
     */
    public function serializeAllAgences(): string
    {
        return $this->cache->get(self::CACHE_KEY_ALL, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);

            $this->logger->info('🔄 Cache MISS: Chargement des agences depuis la base de données');

            $agences = $this->agenceRepository->findAll();
            return $this->serializer->serialize($agences, 'json', ['groups' => 'agence:read']);
        });
    }

    /**
     * Sérialise toutes les agences au format JSON pour les dropdowns.
     * Version OPTIMISÉE qui utilise une requête SQL directe et un cache.
     * 
     * Cette méthode est 10-20x plus rapide que la version avec sérialisation d'objets.
     *
     * @return string JSON des agences
     */
    public function serializeAgencesForDropdown(): string
    {
        return $this->cache->get(self::CACHE_KEY_DROPDOWN, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_TTL);

            $startTime = microtime(true);
            $this->logger->info('🔄 Cache MISS: Génération du JSON des agences pour dropdown');

            // Utiliser la méthode optimisée qui retourne directement un tableau
            $agences = $this->agenceRepository->findAllForDropdownOptimized();

            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $this->logger->info("✅ Agences chargées en {$duration}ms", [
                'count' => count($agences),
                'duration_ms' => $duration
            ]);

            // Encoder directement en JSON (pas besoin du Serializer)
            return json_encode($agences, JSON_THROW_ON_ERROR);
        });
    }

    /**
     * Sérialise toutes les agences au format JSON avec des groupes de sérialisation personnalisés
     *
     * @param array $groups Groupes de sérialisation à utiliser
     * @return string JSON des agences
     */
    public function serializeAllAgencesWithGroups(array $groups): string
    {
        $cacheKey = 'agences_' . md5(implode('_', $groups));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($groups) {
            $item->expiresAfter(self::CACHE_TTL);

            $agences = $this->agenceRepository->findAll();
            return $this->serializer->serialize($agences, 'json', ['groups' => $groups]);
        });
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

    /**
     * Invalide le cache des agences
     * À appeler après toute modification des agences ou services
     */
    public function clearCache(): void
    {
        $this->cache->delete(self::CACHE_KEY_DROPDOWN);
        $this->cache->delete(self::CACHE_KEY_ALL);

        $this->logger->info('🗑️ Cache des agences invalidé');
    }
}
