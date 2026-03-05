<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use Psr\Log\LoggerInterface;
use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;

class OrsBlockingConditionService
{
    private LoggerInterface $logger;
    private OrsModel $orsModel;

    public function __construct(LoggerInterface $logger, OrsModel $orsModel)
    {
        $this->logger = $logger;
        $this->orsModel = $orsModel;
    }

    public function checkBlockingConditionsAvantSoumissionForm(OrsDto $orsDto): ?string
    {
        return null;
    }
}
