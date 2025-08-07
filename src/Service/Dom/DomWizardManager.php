<?php
// src/Service/DomWizardManager.php
namespace App\Service\Dom;

use App\Dto\Dom\DomFirstFormData;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DomWizardManager
{
    private const SESSION_KEY = 'dom_wizard_data';
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Sauvegarde les données de l'étape 1
     * Utilise les données brutes au lieu de sérialiser l'objet complet
     */
    public function saveStep1Data(DomFirstFormData $dto): void
    {
        try {
            // Extraire uniquement les données nécessaires (pas d'entités Doctrine)
            $data = [
                // Replace with the correct method/property for agenceEmetteur
                'sousTypeDocument' => $dto->getSousTypeDocument() ? $dto->getSousTypeDocument()->getId() : null,
                'salarie' => $dto->getSalarie(),
                'categorie' => $dto->getCategorie() ? $dto->getCategorie()->getId() : null,
                'matriculeNom' => $dto->getMatriculeNom() ? $dto->getMatriculeNom()->getId() : null,
                'matricule' => $dto->getMatricule(),
                'nom' => $dto->getNom(),
                'prenom' => $dto->getPrenom(),
                'cin' => $dto->getCin(),
            ];

            $this->session->set(self::SESSION_KEY, $data);
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la sauvegarde des données du wizard: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les données de l'étape 1
     * Retourne un tableau de données brutes
     */
    public function getStep1DataArray(): ?array
    {
        if (!$this->session->has(self::SESSION_KEY)) {
            return null;
        }

        $data = $this->session->get(self::SESSION_KEY);

        // Vérifier la validité des données (optionnel: timeout après 1h)
        if (isset($data['timestamp']) && (time() - $data['timestamp'] > 3600)) {
            $this->clear();
            return null;
        }

        return $data;
    }

    /**
     * Reconstruit le DTO à partir des données sauvegardées
     * Cette méthode sera utilisée dans le contrôleur avec les repositories
     */
    public function hasStep1Data(): bool
    {
        return $this->session->has(self::SESSION_KEY) && !empty($this->session->get(self::SESSION_KEY));
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
