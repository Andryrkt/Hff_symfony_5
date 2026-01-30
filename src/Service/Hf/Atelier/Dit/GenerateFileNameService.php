<?php

namespace App\Service\Hf\Atelier\Dit;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\Utils\Fichier\AbstractFileNameGeneratorService;

class GenerateFileNameService extends AbstractFileNameGeneratorService
{
    public function generateFileUplodeName(
        UploadedFile $file,
        string $numero,
        string $codeAgenceServiceUser,
        int $index = 1
    ): string {
        return $this->generateFileName($file, [
            'format' => '{numero}_{codeAgenceServiceUser}.{extension}',
            'variables' => [
                'numero' => $numero,
                'codeAgenceServiceUser' => $codeAgenceServiceUser
            ],
            'sauter_premier_index' => false // Ne pas sauter le premier index
        ], $index);
    }

    public function generateMainName(string $numero, string $codeAgenceServiceUser): string
    {
        return $numero . '_' . $codeAgenceServiceUser . '.pdf';
    }
}
