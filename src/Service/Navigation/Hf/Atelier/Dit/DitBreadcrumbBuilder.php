<?php

namespace App\Service\Navigation\Hf\Atelier\Dit;

use App\Service\Navigation\BreadcrumbBuilder;
use Symfony\Component\Security\Core\Security;
use App\Service\Navigation\Hf\BaseBreadcrumbBuilder;
use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use App\Contract\Navigation\BreadcrumbBuilderInterface;

class DitBreadcrumbBuilder extends BaseBreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    private $breadcrumb;

    public function __construct(
        BreadcrumbBuilder $breadcrumb,
        VignetteRepository $vignetteRepository,
        Security $security
    ) {
        parent::__construct($vignetteRepository, $security);
        $this->breadcrumb = $breadcrumb;
    }

    public function supports(string $context): bool
    {
        return in_array($context, [
            'hf_atelier_dit_liste_index',
            'hf_atelier_dit_form_index',
            'hf_atelier_dit_creation_duplication'
        ]);
    }

    public function build(array $parameters = []): array
    {
        $context = $parameters['context'] ?? 'dom_liste_index';

        $items = [];
        $backConfig = [];

        switch ($context) {
            case 'hf_atelier_dit_form_index':
                $items = $this->buildFormBreadcrumb();
                break;
            case 'hf_atelier_dit_liste_index':
                $items = $this->buildListeBreadcrumb();
                break;
            case 'hf_atelier_dit_creation_duplication':
                $items = $this->buildDuplicationBreadcrumb();
                break;
            default:
                throw new \InvalidArgumentException("Unsupported product context: {$context}");
        }

        // Récupérer la config du bouton retour
        $backConfig = $this->breadcrumb->getBackConfig();

        // Fusionner items et config pour le retour
        return array_merge(['items' => $items], $backConfig);
    }

    private function buildBaseBreadcrumb(): BreadcrumbBuilder
    {

        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'home_index', [], [], 'fas fa-home')
            ->add('HFF', null, [], $this->hfSubmenu(), 'fas fa-building')
            ->add('ATELIER', null, [], $this->atelierSubmenu(), 'fas fa-folder')
            ->add('DIT', null, [], $this->ditSubmenu(), 'fas fa-folder')
        ;
    }

    private function buildFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création dit")
            ->setBackRoute('hf_atelier_dit_liste_index')
            ->get();
    }

    private function buildDuplicationBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Duplication dit")
            ->setBackRoute('hf_atelier_dit_liste_index')
            ->get();
    }

    private function buildListeBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Liste de consultation du dit")
            ->setBackRoute('home_index')
            ->get();
    }
}
