<?php

namespace App\Service\Hf\Rh\Dom;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\Utils\Fichier\AbstractFileNameGeneratorService;


class DomGenerateFileNameService extends AbstractFileNameGeneratorService
{
    public function generateFileUplodeName(
        UploadedFile $file,
        string $numDom,
        string $codeAgenceServiceUser,
        int $index = 1
    ): string {
        return $this->generateFileName($file, [
            'format' => '{numDom}_{codeAgenceServiceUser}.{extension}',
            'variables' => [
                'numDom' => $numDom,
                'codeAgenceServiceUser' => $codeAgenceServiceUser
            ],
            'sauter_premier_index' => false // Ne pas sauter le premier index
        ], $index);
    }

    public function generateMainName(
        string $numDom,
        string $codeAgenceServiceUser
    ) {
        return $numDom . '_' . $codeAgenceServiceUser . '.pdf';
    }
}
