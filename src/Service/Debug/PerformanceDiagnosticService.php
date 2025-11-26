<?php

namespace App\Service\Debug;

use Psr\Log\LoggerInterface;

/**
 * Service de diagnostic de performance
 * Permet de mesurer et logger le temps d'exécution de différentes opérations
 */
class PerformanceDiagnosticService
{
    private LoggerInterface $logger;
    private array $timers = [];
    private array $measurements = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Démarre un timer pour une opération
     */
    public function startTimer(string $operationName): void
    {
        $this->timers[$operationName] = microtime(true);
    }

    /**
     * Arrête un timer et enregistre la mesure
     */
    public function stopTimer(string $operationName, array $context = []): float
    {
        if (!isset($this->timers[$operationName])) {
            $this->logger->warning("Timer '{$operationName}' n'a pas été démarré");
            return 0.0;
        }

        $duration = (microtime(true) - $this->timers[$operationName]) * 1000; // en millisecondes

        $this->measurements[$operationName] = [
            'duration_ms' => round($duration, 2),
            'context' => $context
        ];

        $this->logger->info("⏱️ Performance: {$operationName}", [
            'duration_ms' => round($duration, 2),
            'context' => $context
        ]);

        unset($this->timers[$operationName]);

        return $duration;
    }

    /**
     * Mesure l'exécution d'une fonction callable
     */
    public function measure(string $operationName, callable $callback, array $context = [])
    {
        $this->startTimer($operationName);

        try {
            $result = $callback();
            $this->stopTimer($operationName, $context);
            return $result;
        } catch (\Throwable $e) {
            $this->stopTimer($operationName, array_merge($context, ['error' => $e->getMessage()]));
            throw $e;
        }
    }

    /**
     * Retourne toutes les mesures effectuées
     */
    public function getMeasurements(): array
    {
        return $this->measurements;
    }

    /**
     * Retourne un résumé des mesures
     */
    public function getSummary(): array
    {
        $total = array_sum(array_column($this->measurements, 'duration_ms'));

        $summary = [
            'total_duration_ms' => round($total, 2),
            'operations_count' => count($this->measurements),
            'slowest_operation' => null,
            'measurements' => $this->measurements
        ];

        if (!empty($this->measurements)) {
            $slowest = array_reduce(
                array_keys($this->measurements),
                fn($carry, $key) => ($carry === null || $this->measurements[$key]['duration_ms'] > $this->measurements[$carry]['duration_ms'])
                    ? $key
                    : $carry
            );

            $summary['slowest_operation'] = [
                'name' => $slowest,
                'duration_ms' => $this->measurements[$slowest]['duration_ms']
            ];
        }

        return $summary;
    }

    /**
     * Log le résumé complet
     */
    public function logSummary(): void
    {
        $summary = $this->getSummary();

        $this->logger->info("📊 Résumé des performances", $summary);

        if ($summary['slowest_operation']) {
            $this->logger->warning("🐌 Opération la plus lente: {$summary['slowest_operation']['name']}", [
                'duration_ms' => $summary['slowest_operation']['duration_ms']
            ]);
        }
    }

    /**
     * Réinitialise toutes les mesures
     */
    public function reset(): void
    {
        $this->timers = [];
        $this->measurements = [];
    }
}
