<?php

namespace App\Service\Utils;

use App\Constants\Hf\Atelier\Dit\Soumission\Ors\StatutComparaisonConstant;

/**
 * Service utilitaire pour comparer deux collections d'objets.
 * Utile pour identifier les ajouts (Nouv), suppressions (Supp) ou modifications (Modif).
 */
class CollectionComparatorService
{
    /**
     * Compare deux tableaux d'objets ou de tableaux.
     * 
     * @param array         $before          Collection initiale (Avant)
     * @param array         $after           Collection finale (Après)
     * @param callable      $idExtractor     Fonction pour extraire l'identifiant unique d'un élément
     * @param callable|null $changeDetector  Fonction pour détecter si un élément a été modifié (reçoit $before, $after)
     * 
     * @return array Un tableau associatif [id => ['before' => object|null, 'after' => object|null, 'status' => string]]
     */
    public function compare(array $before, array $after, callable $idExtractor, ?callable $changeDetector = null): array
    {
        $beforeMap = $this->indexById($before, $idExtractor);
        $afterMap = $this->indexById($after, $idExtractor);

        $allKeys = array_unique(array_merge(array_keys($beforeMap), array_keys($afterMap)));
        sort($allKeys);

        $results = [];
        foreach ($allKeys as $key) {
            $av = $beforeMap[$key] ?? null;
            $ap = $afterMap[$key] ?? null;

            $status = $this->resolveStatus($av, $ap, $changeDetector);

            $results[$key] = [
                'before' => $av,
                'after'  => $ap,
                'status' => $status
            ];
        }

        return $results;
    }

    /**
     * Indexe une collection par son identifiant unique.
     */
    private function indexById(array $collection, callable $idExtractor): array
    {
        $map = [];
        foreach ($collection as $item) {
            $map[$idExtractor($item)] = $item;
        }
        return $map;
    }

    /**
     * Détermine le statut de base d'un élément dans la comparaison.
     */
    private function resolveStatus($before, $after, ?callable $changeDetector): string
    {
        if (null === $before && null !== $after) {
            return StatutComparaisonConstant::NOUVEAU;
        }

        if (null !== $before && null === $after) {
            return StatutComparaisonConstant::SUPPRIME;
        }

        if ($changeDetector && $changeDetector($before, $after)) {
            return StatutComparaisonConstant::MODIFIE;
        }

        return StatutComparaisonConstant::IDENTIQUE;
    }
}
