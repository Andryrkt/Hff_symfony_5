<?php

// src/Service/DomWizardManager.php

namespace App\Service\Dom;


use App\Dto\Dom\DomFirstFormData;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DomWizardManager
{
    private const SESSION_KEY = 'dom_wizard_data';

    private SessionInterface $session;
    private SerializerInterface $serializer;

    public function __construct(
        SessionInterface $session,
        SerializerInterface $serializer
    ) {
        $this->session = $session;
        $this->serializer = $serializer;
    }

    /**
     * Sauvegarde les données de l'étape 1
     */
    public function saveStep1Data(DomFirstFormData $dto): void
    {
        $context = [
            // Exemple de contexte personnalisé si nécessaire
            'circular_reference_handler' => function ($object) {
                return $object->getId(); // Ou autre identifiant
            }
        ];

        $serialized = $this->serializer->serialize($dto, 'json', $context);
        $this->session->set(self::SESSION_KEY, $serialized);
    }

    /**
     * Récupère les données de l'étape 1
     */
    public function getStep1Data(): ?DomFirstFormData
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            return null;
        }

        try {
            return $this->serializer->deserialize(
                $this->session->get(self::SESSION_KEY),
                DomFirstFormData::class,
                'json'
            );
        } catch (\Exception $e) {
            $this->clear();
            return null;
        }
    }

    /**
     * Vérifie si des données d'étape 1 existent
     */
    public function hasStep1Data(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

    /**
     * Nettoie les données du workflow
     */
    public function clear(): void
    {
        $this->session->remove(self::SESSION_KEY);
    }

    /**
     * Valide la transition entre étapes
     */
    public function validateTransition(string $currentStep, string $targetStep): bool
    {
        $allowedTransitions = [
            'step1' => ['step2'],
            'step2' => ['step3', 'step1'],
            'step3' => ['confirm', 'step2']
        ];

        return in_array($targetStep, $allowedTransitions[$currentStep] ?? []);
    }
}
