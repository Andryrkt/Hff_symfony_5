<?php

namespace App\Service\Home;

use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use App\Service\Traits\MaterielSubmenuTrait;
use App\Service\Traits\ItSubmenuTrait;
use App\Service\Traits\RhSubmenuTrait;
use App\Service\Traits\HseSubmenuTrait;
use App\Service\Traits\PolSubmenuTrait;
use App\Service\Traits\ApproSubmenuTrait;
use App\Service\Traits\ComptaSubmenuTrait;
use App\Service\Traits\AtelierSubmenuTrait;
use App\Service\Traits\EnergieSubmenuTrait;
use App\Service\Traits\MagasinSubmenuTrait;
use App\Service\Traits\ReportingSubmenuTrait;
use App\Service\Traits\DocumentationSubmenuTrait;
use Symfony\Component\Security\Core\Security;

class HomeCardService
{
    use MaterielSubmenuTrait;
    use ItSubmenuTrait;
    use RhSubmenuTrait;
    use HseSubmenuTrait;
    use PolSubmenuTrait;
    use ApproSubmenuTrait;
    use ComptaSubmenuTrait;
    use AtelierSubmenuTrait;
    use EnergieSubmenuTrait;
    use MagasinSubmenuTrait;
    use ReportingSubmenuTrait;
    use DocumentationSubmenuTrait;

    private $vignetteRepository;
    private $security;

    public function __construct(VignetteRepository $vignetteRepository, Security $security)
    {
        $this->vignetteRepository = $vignetteRepository;
        $this->security = $security;
    }

    public function getHomeCards(): array
    {
        $vignettes = $this->vignetteRepository->findForHomeCards();
        $cards = [];

        foreach ($vignettes as $vignette) {
            if ($this->security->isGranted('APPLICATION_ACCESS', $vignette)) {
                $cardData = $this->getCardData($vignette->getNom());
                $cards[] = new HomeCard(
                    $vignette->getNom(),
                    $vignette->getDescription() ?? '',
                    $cardData['icon'],
                    $cardData['color'],
                    $this->getLinksForVignette($vignette->getNom())
                );
            }
        }

        return $cards;
    }

    private function getCardData(string $vignetteName): array
    {
        $cardData = [
            'Documentation' => ['icon' => 'fas fa-book', 'color' => 'success'],
            'Reporting' => ['icon' => 'fas fa-chart-line', 'color' => 'info'],
            'Compta' => ['icon' => 'fas fa-file-invoice-dollar', 'color' => 'danger'],
            'RH' => ['icon' => 'fas fa-users', 'color' => 'warning'],
            'Matériel' => ['icon' => 'fas fa-truck-pickup', 'color' => 'secondary'],
            'Atelier' => ['icon' => 'fas fa-tools', 'color' => 'secondary'],
            'Magasin' => ['icon' => 'fas fa-warehouse', 'color' => 'secondary'],
            'Appro' => ['icon' => 'fas fa-shopping-basket', 'color' => 'secondary'],
            'IT' => ['icon' => 'fas fa-laptop', 'color' => 'secondary'],
            'POL' => ['icon' => 'fas fa-gas-pump', 'color' => 'secondary'],
            'Energie' => ['icon' => 'fas fa-bolt', 'color' => 'secondary'],
            'HSE' => ['icon' => 'fas fa-hard-hat', 'color' => 'secondary'],
        ];

        return $cardData[$vignetteName] ?? ['icon' => 'fas fa-question-circle', 'color' => 'secondary'];
    }

    private function transformSubmenu(array $submenu): array
    {
        return array_map(function ($item) {
            if (isset($item['submenu'])) {
                $item['children'] = $this->transformSubmenu($item['submenu']);
                unset($item['submenu']);
            }
            return $item;
        }, $submenu);
    }

    private function getLinksForVignette(string $vignetteName): array
    {
        $links = [];

        switch ($vignetteName) {
            case 'Documentation':
                $links = $this->documentationSubmenu();
                break;
            case 'Reporting':
                $links = $this->reportingSubmenu();
                break;
            case 'Compta':
                $links = $this->comptaSubmenu();
                break;
            case 'RH':
                $links = $this->rhSubmenu();
                break;
            case 'Matériel':
                $links = $this->materielSubmenu();
                break;
            case 'Atelier':
                $links = $this->atelierSubmenu();
                break;
            case 'Magasin':
                $links = $this->magasinSubmenu();
                break;
            case 'Appro':
                $links = $this->approSubmenu();
                break;
            case 'IT':
                $links = $this->itSubmenu();
                break;
            case 'POL':
                $links = $this->polSubmenu();
                break;
            case 'Energie':
                $links = $this->energieSubmenu();
                break;
            case 'HSE':
                $links = $this->hseSubmenu();
                break;
            default:
                $links = [];
                break;
        }

        return $this->transformSubmenu($links);
    }

    public function getCardByIndex(int $index): ?HomeCard
    {
        $cards = $this->getHomeCards();
        return $cards[$index] ?? null;
    }

    public function getCardByName(string $name): ?HomeCard
    {
        $vignette = $this->vignetteRepository->findOneForHomeCard($name);

        if (!$vignette || !$this->security->isGranted('APPLICATION_ACCESS', $vignette)) {
            return null;
        }

        $cardData = $this->getCardData($vignette->getNom());

        return new HomeCard(
            $vignette->getNom(),
            $vignette->getDescription() ?? '',
            $cardData['icon'],
            $cardData['color'],
            $this->getLinksForVignette($vignette->getNom())
        );
    }
}
