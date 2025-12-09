<?php

namespace App\Service\Migration\Utils;

use Psr\Log\LoggerInterface;

/**
 * Service utilitaire pour les conversions de dates et heures
 */
class DateTimeConverter
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Convertit une valeur en DateTime (conserve l'heure)
     */
    public function convertToDateTime($value): ?\DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_string($value)) {
            try {
                return new \DateTime($value);
            } catch (\Exception $e) {
                $this->logger->warning('Impossible de convertir la date/heure', [
                    'value' => $value,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        }

        return null;
    }

    /**
     * Convertit une valeur en Date (perd l'heure)
     */
    public function convertToDate($value): ?\DateTimeInterface
    {
        $datetime = $this->convertToDateTime($value);

        if ($datetime) {
            // Réinitialise l'heure à 00:00:00
            return new \DateTime($datetime->format('Y-m-d'));
        }

        return null;
    }
}
