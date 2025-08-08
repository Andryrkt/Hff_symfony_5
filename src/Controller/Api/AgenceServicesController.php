<?php

namespace App\Controller\Api;

use App\Entity\Admin\AgenceService\Agence;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AgenceServicesController
{
    public function __invoke(Agence $data): iterable
    {
        return $data->getServices();
    }
}
