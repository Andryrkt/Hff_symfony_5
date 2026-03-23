<?php

namespace App\Service\Hf\Materiel\Casier;

use Psr\Log\LoggerInterface;
use App\Entity\Hf\Materiel\Casier\Casier;
use Symfony\Component\Form\FormInterface;
use App\Dto\Hf\Materiel\Casier\SecondFormDto;
use App\Factory\Hf\Materiel\Casier\CasierFactory;
use App\Repository\Hf\Materiel\Casier\CasierRepository;
use App\Constants\Admin\Historisation\TypeDocumentConstants;
use App\Constants\Admin\Historisation\TypeOperationConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

class CasierCreationHandler
{
    private CasierFactory $casierFactory;
    private CasierRepository $casierRepository;
    private HistoriqueOperationService $historiqueOperationService;
    private LoggerInterface $logger;

    public function __construct(
        CasierFactory $casierFactory,
        CasierRepository $casierRepository,
        HistoriqueOperationService $historiqueOperationService,
        LoggerInterface $casierCreationLogger
    ) {
        $this->casierFactory = $casierFactory;
        $this->casierRepository = $casierRepository;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->logger = $casierCreationLogger;
    }
    public function handle(FormInterface $form): Casier
    {
        /** @var SecondFormDto $secondFormDto */
        $secondFormDto = $form->getData();

        $casier = $this->casierFactory->create($secondFormDto);

        $this->saveCasier($casier);
        return $casier;
    }

    private function saveCasier(Casier $casier): void
    {
        $success = false;
        $message = 'Enregistrement dans la base de données.';
        try {
            $this->casierRepository->add($casier, true);
            $success = true;
            $message = 'Enregistrement dans la base de données réussi.';
            $this->logger->info($message, ['numero_casier' => $casier->getNumero()]);
        } catch (\Exception $e) {
            $message = 'Erreur lors de l\'enregistrement dans la base de données : ' . $e->getMessage();
            $this->logger->error($message, ['numero_casier' => $casier->getNumero(), 'exception' => $e]);
            throw $e;
        } finally {
            $this->historiqueOperationService->enregistrer(
                $casier->getNumero(),
                TypeOperationConstants::TYPE_OPERATION_DB_SAVE_NAME,
                TypeDocumentConstants::TYPE_DOCUMENT_CAS_CODE,
                $success,
                $message
            );
        }
    }
}
