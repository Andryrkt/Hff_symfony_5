<?php

namespace App\Service\Historique_operation;

use App\Entity\Admin\Historisation\HistoriqueOperationDocument;
use App\Repository\Admin\Historisation\TypeDocumentRepository;
use App\Repository\Admin\Historisation\TypeOperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Service pour gérer la création des entrées dans l'historique des opérations.
 * Ce service a une unique responsabilité : créer un enregistrement.
 * La logique de redirection et de messages flash doit être gérée par les contrôleurs.
 */
class HistoriqueOperationService
{
    private EntityManagerInterface $em;
    private Security $security;
    private TypeOperationRepository $typeOperationRepository;
    private TypeDocumentRepository $typeDocumentRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        TypeOperationRepository $typeOperationRepository,
        TypeDocumentRepository $typeDocumentRepository,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->typeOperationRepository = $typeOperationRepository;
        $this->typeDocumentRepository = $typeDocumentRepository;
        $this->logger = $logger;
    }

    /**
     * Enregistre une opération dans l'historique.
     *
     * @param string $numeroDocument Le numéro du document.
     * @param int $typeOperation Le nom de l'opération (ex: 'SOUMISSION', 'CREATION').
     * @param string $typeDocumentCode Le code unique du type de document (ex: 'DIT', 'OR').
     * @param bool $isSuccess Le statut de l'opération (true pour succès, false pour échec).
     * @param string|null $libelleOperation Un libellé décrivant l'opération.
     */
    public function enregistrer(
        string $numeroDocument,
        string $typeOperation,
        string $typeDocumentCode,
        bool $isSuccess,
        ?string $libelleOperation = null
    ): void {
        $user = $this->security->getUser();
        if (!$user) {
            $this->logger->warning('Tentative d\'enregistrement d\'historique sans utilisateur connecté.');
            throw new \RuntimeException('Aucun utilisateur connecté pour enregistrer l\'historique.');
        }

        $typeOperationEntity = $this->typeOperationRepository->findOneBy(['typeOperation' => $typeOperation]);
        $typeDocumentEntity = $this->typeDocumentRepository->findOneBy(['typeDocument' => $typeDocumentCode]);

        if (!$typeOperationEntity) {
            $this->logger->error(sprintf('Type d\'opération "%s" non trouvé pour l\'historique du document "%s".', $typeOperation, $numeroDocument));
            throw new \InvalidArgumentException(sprintf('Le type d\'opération "%s" est invalide.', $typeOperation));
        }

        if (!$typeDocumentEntity) {
            $this->logger->error(sprintf('Type de document "%s" non trouvé pour l\'historique du document "%s".', $typeDocumentCode, $numeroDocument));
            throw new \InvalidArgumentException(sprintf('Le code de type de document "%s" est invalide.', $typeDocumentCode));
        }

        if (!$this->em->isOpen()) {
            $this->logger->error(sprintf('Impossible d\'enregistrer l\'historique pour le document "%s" : l\'EntityManager est fermé.', $numeroDocument));
            return;
        }

        $historique = new HistoriqueOperationDocument();
        $historique
            ->setNumeroDocument($numeroDocument)
            ->setUtilisateur($user->getUserIdentifier())
            ->setTypeOperation($typeOperationEntity)
            ->setTypeDocument($typeDocumentEntity)
            ->setStatutOperation($isSuccess ? 'Succès' : 'Echec')
            ->setLibelleOperation($libelleOperation);

        try {
            $this->em->persist($historique);
            $this->em->flush();
            $this->logger->info(sprintf('Opération "%s" sur document "%s" enregistrée avec succès par "%s".', $typeOperation, $numeroDocument, $user->getUserIdentifier()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Erreur lors de l\'enregistrement de l\'historique pour le document "%s" : %s', $numeroDocument, $e->getMessage()));
            // On ne relance pas d'exception ici pour ne pas masquer l'erreur initiale si ce service est appelé dans un bloc finally
        }
    }
}
