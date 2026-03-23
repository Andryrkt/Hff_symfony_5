<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Service\Utils\Fichier\AbstractFileNameGeneratorService;
use Symfony\Component\HttpFoundation\File\UploadedFile;



class OrsGenerateFileNameService extends AbstractFileNameGeneratorService
{
    public function generateFileUplodeName(
        UploadedFile $file,
        string $numeroOr,
        string $numeroVersion,
        int $index = 1
    ): string {
        return $this->generateFileName($file, [
            'format' => 'oRValidation_{numeroOr}-{numeroVersion}_PJ.{extension}',
            'variables' => [
                'numeroOr' => $numeroOr,
                'numeroVersion' => $numeroVersion
            ],
            'sauter_premier_index' => true // Sauter le premier car c'est _PJ.jpg, puis _PJ_01.jpg
        ], $index);
    }

    public function generateMainName(string $numeroOr, string $numeroVersion, string $suffix): string
    {
        return 'oRValidation_' . $numeroOr . '-' . $numeroVersion . '#' . $suffix . '.pdf';
    }
}
