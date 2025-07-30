<?php

namespace App\Contract;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;

interface AgencyServiceAwareInterface
{
    public function getEmitterAgence(): ?Agence;
    public function getEmitterService(): ?Service;
    public function getDebtorAgence(): ?Agence;
    public function getDebtorService(): ?Service;
}
