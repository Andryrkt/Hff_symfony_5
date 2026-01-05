<?php

namespace App\Service\Navigation\Hf;

use App\Service\Traits\ItSubmenuTrait;
use App\Service\Traits\RhSubmenuTrait;
use App\Service\Traits\HseSubmenuTrait;
use App\Service\Traits\PolSubmenuTrait;
use App\Service\Traits\ApproSubmenuTrait;
use App\Service\Traits\ComptaSubmenuTrait;
use App\Service\Traits\AtelierSubmenuTrait;
use App\Service\Traits\EnergieSubmenuTrait;
use App\Service\Traits\MagasinSubmenuTrait;
use App\Service\Traits\MaterielSubmenuTrait;
use App\Service\Traits\ReportingSubmenuTrait;
use Symfony\Component\Security\Core\Security;
use App\Service\Traits\DocumentationSubmenuTrait;
use App\Repository\Admin\ApplicationGroupe\VignetteRepository;

class BaseBreadcrumbBuilder
{
    use RhSubmenuTrait;
    use ComptaSubmenuTrait;
    use ReportingSubmenuTrait;
    use EnergieSubmenuTrait;
    use HseSubmenuTrait;
    use MagasinSubmenuTrait;
    use ApproSubmenuTrait;
    use ItSubmenuTrait;
    use PolSubmenuTrait;
    use DocumentationSubmenuTrait;
    use MaterielSubmenuTrait;
    use AtelierSubmenuTrait;

    protected $vignetteRepository;
    protected $security;

    public function __construct(VignetteRepository $vignetteRepository, Security $security)
    {
        $this->vignetteRepository = $vignetteRepository;
        $this->security = $security;
    }

    /**
     * Filtre les sous-menus en fonction des permissions de l'utilisateur
     * Vérifie l'accès à la vignette correspondante
     */
    private array $localVignetteCache = [];

    /**
     * Filtre les sous-menus en fonction des permissions de l'utilisateur
     * Vérifie l'accès à la vignette correspondante
     */
    protected function filterSubmenuByPermissions(string $vignetteName, array $submenu): array
    {
        // Chargement initial du cache local si vide
        if (empty($this->localVignetteCache)) {
            $vignettes = $this->vignetteRepository->findForHomeCards();
            foreach ($vignettes as $v) {
                $this->localVignetteCache[$v->getNom()] = $v;
            }
        }

        // Récupérer la vignette depuis le cache local (plus de requête SQL ici)
        $vignette = $this->localVignetteCache[$vignetteName] ?? null;

        // Si la vignette n'existe pas ou si l'utilisateur n'a pas accès, retourner un tableau vide
        if (!$vignette || !$this->security->isGranted('APPLICATION_ACCESS', $vignette)) {
            return [];
        }

        // Si l'utilisateur a accès, retourner le sous-menu complet
        return $submenu;
    }


    protected function hfSubmenu(): array
    {
        return array_filter([
            /** =============== Documentation ===================== */
            [
                'label' => 'documentation',
                'icon' => 'fas fa-book',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Documentation', $this->documentationSubmenu())
            ],
            /** ======== Reporting ========== */
            [
                'label' => 'Reporting',
                'icon' => 'fas fa-chart-line',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Reporting', $this->reportingSubmenu())
            ],
            /** ======== Compta ========== */
            [
                'label' => 'Compta',
                'icon' => 'fas fa-file-invoice-dollar',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Compta', $this->comptaSubmenu())
            ],
            /** ======== RH ========== */
            [
                'label' => 'rh',
                'icon' => 'fas fa-users',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('RH', $this->rhSubmenu())
            ],
            /** ======== Matériel ========== */
            [
                'label' => 'Matériel',
                'icon' => 'fas fa-truck-pickup',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Matériel', $this->materielSubmenu())
            ],
            /** ======== Atelier ========== */
            [
                'label' => 'Atelier',
                'icon' => 'fas fa-tools',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Atelier', $this->atelierSubmenu())
            ],
            /** ======== Magasin ========== */
            [
                'label' => 'Magasin',
                'icon' => 'fas fa-warehouse',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Magasin', $this->magasinSubmenu())
            ],
            /** ======== Appro ========== */
            [
                'label' => 'Appro',
                'icon' => 'fas fa-shopping-basket',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Appro', $this->approSubmenu())
            ],
            /** ======== IT ========== */
            [
                'label' => 'IT',
                'icon' => 'fas fa-laptop',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('IT', $this->itSubmenu())
            ],
            /** ======== POL ========== */
            [
                'label' => 'POL',
                'icon' => 'fas fa-gas-pump',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('POL', $this->polSubmenu())
            ],
            /** ======== Energie ========== */
            [
                'label' => 'Energie',
                'icon' => 'fas fa-bolt',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('Energie', $this->energieSubmenu())
            ],
            /** ======== HSE ========== */
            [
                'label' => 'HSE',
                'icon' => 'fas fa-hard-hat',
                'route' => null, // C'est un conteneur de sous-menu
                'submenu' => $this->filterSubmenuByPermissions('HSE', $this->hseSubmenu())
            ],
        ], function ($item) {
            // Filtrer les éléments dont le sous-menu est vide (pas d'accès)
            return !empty($item['submenu']);
        });
    }
}
