<?php

namespace App\Service\Utils;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\ApplicationGroupe\SequenceAppllication;
use App\Repository\Admin\ApplicationGroupe\SequenceAppllicationRepository;

class NumeroGeneratorService
{
    // private const PADDING_CONFIG = [
    //     'CAS' => 4,  // CAS25110001 (4 chiffres)
    // 'DOM' => 6,  // DOM2511000001 (6 chiffres) 
    // 'AUT' => 4,  // AUT25110001 (4 chiffres)
    // ];

    private EntityManagerInterface $entityManager;
    private SequenceAppllicationRepository $sequenceRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SequenceAppllicationRepository $sequenceRepository
    ) {
        $this->entityManager = $entityManager;
        $this->sequenceRepository = $sequenceRepository;
    }

    public function autoGenerateNumero(string $codeApp, bool $increment = true): string
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            // 1. Récupérer et verrouiller la séquence
            $sequence = $this->sequenceRepository->findOneByCodeAppWithLock($codeApp);

            // 2. Calculer le nouveau numéro
            if (!$sequence) {
                $nouveauNumero = 1;
                $sequence = $this->creerSequence($codeApp, date('ym'), $nouveauNumero);
            } else {
                $nouveauNumero = $this->calculerNouveauNumero($sequence, date('ym'), $increment);
                $this->mettreAJourSequence($sequence, date('ym'), $nouveauNumero);
            }

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            // 3. Formater et retourner
            return $this->formaterNumero($codeApp, date('ym'), $nouveauNumero);
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    public function simpleIncrement(?int $num): int
    {
        return ($num ?? 0) + 1;
    }

    public function simpleDecrement(?int $num): int
    {
        return ($num ?? 0) - 1;
    }

    private function calculerNouveauNumero(SequenceAppllication $sequence, string $anneeMoisCourant, bool $increment): int
    {
        $dernierNumero = $sequence->getDernierNumero();
        $anneeMoisSequence = $sequence->getAnneeMois();

        if ($anneeMoisSequence === $anneeMoisCourant) {
            return $increment ? $dernierNumero + 1 : $dernierNumero - 1;
        } else {
            if ($anneeMoisCourant > $anneeMoisSequence) {
                return $increment ? 1 : $this->getMaxNumero($sequence->getCodeApp());
            } else {
                return $increment ? $dernierNumero + 1 : $dernierNumero - 1;
            }
        }
    }

    private function creerSequence(string $codeApp, string $anneeMois, int $numero): SequenceAppllication
    {
        $sequence = new SequenceAppllication($codeApp, $anneeMois, $numero);
        $this->entityManager->persist($sequence);
        return $sequence;
    }

    private function mettreAJourSequence(SequenceAppllication $sequence, string $anneeMois, int $numero): void
    {
        $sequence->setAnneeMois($anneeMois);
        $sequence->setDernierNumero($numero);
    }

    private function formaterNumero(string $codeApp, string $anneeMois, int $numero): string
    {
        // $padding = self::PADDING_CONFIG[$codeApp] ?? 4;
        $padding = 4;
        $numeroFormate = str_pad($numero, $padding, '0', STR_PAD_LEFT);

        return $codeApp . $anneeMois . $numeroFormate;
    }

    private function getMaxNumero(string $codeApp): int
    {
        // $padding = self::PADDING_CONFIG[$codeApp] ?? 4;
        $padding = 4;
        return (int) str_pad('9', $padding, '9');
    }
}
