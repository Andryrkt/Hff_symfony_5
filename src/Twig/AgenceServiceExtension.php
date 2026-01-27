<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Service\Admin\AgenceSerializerService;

class AgenceServiceExtension extends AbstractExtension
{
    private AgenceSerializerService $agenceSerializerService;

    public function __construct(AgenceSerializerService $agenceSerializerService)
    {
        $this->agenceSerializerService = $agenceSerializerService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('agence_service_data', [$this, 'renderAgenceServiceData'], ['is_safe' => ['html']]),
        ];
    }

    public function renderAgenceServiceData(): string
    {
        $data = $this->agenceSerializerService->serializeAgencesForDropdown();
        return sprintf('<div id="agence-service-data" data-agences=\'%s\'></div>', $data);
    }
}
