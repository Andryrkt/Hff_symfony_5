<?php

namespace App\Factory\Hf\Atelier\Dit;

use App\Dto\Hf\Atelier\Dit\FormDto;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ButtonsFactory
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function generateEllipsisButtons(FormDto $dto): array
    {
        $buttons = [];

        // 1. Toujours Dupliquer
        $buttons[] = [
            'label' => 'Dupliquer',
            'route' => 'hf_atelier_dit_creation_duplication',
            'route_params' => ['numDit' => $dto->numeroDit],
        ];

        // 2. Soumission document à valider (si condition remplie)
        if ($dto->estOrASoumi) {
            $buttons[] = [
                'label' => 'Soumission document à valider',
                'url' => '#',
                'class' => 'soumissionDoc fw-bold',
                'attributes' => 'data-bs-toggle="modal" data-bs-target="#soumissionDocModal" data-numdit="' . $dto->numeroDit . '"',
            ];
        }

        // 3. Dossier DIT (si numéro DIT valide)
        if ($dto->numeroDit) {
            $buttons[] = [
                'label' => 'Dossier DIT',
                'route' => 'hf_atelier_dit_dw_intervention',
                'route_params' => ['numDit' => $dto->numeroDit],
                'attributes' => 'target="_blank"',
            ];
        }

        // 4. Clôturer la DIT (si annulable)
        if ($dto->estAnnulable) {
            $buttons[] = [
                'label' => 'Clôturer la DIT',
                'route' => 'hf_atelier_dit_liste_cloture',
                'route_params' => ['numDit' => $dto->numeroDit],
                'class' => 'fw-bold clotureDit',
                'attributes' => 'data-id="' . $dto->numeroDit . '"',
            ];
        }

        return $buttons;
    }
}
