<?php

namespace App\Service\Navigation\Hf\Materiel\Badm;

use App\Service\Navigation\BreadcrumbBuilder;
use Symfony\Component\Security\Core\Security;
use App\Service\Navigation\Hf\BaseBreadcrumbBuilder;
use App\Contract\Navigation\BreadcrumbBuilderInterface;
use App\Repository\Admin\ApplicationGroupe\VignetteRepository;

class BadmBreadcrumbBuilder extends BaseBreadcrumbBuilder implements BreadcrumbBuilderInterface
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
            'hf_materiel_badm_liste_index',
            'hf_materiel_badm_first_form_index',
            'hf_materiel_badm_second_form_index',
        ]);
    }

    public function build(array $parameters = []): array
    {
        $context = $parameters['context'] ?? 'dom_liste_index';

        $items = [];
        $backConfig = [];

        switch ($context) {
            case 'hf_materiel_badm_first_form_index':
                $items = $this->buildFirstFormBreadcrumb();
                break;
            case 'hf_materiel_badm_second_form_index':
                $items = $this->buildSecondFormBreadcrumb();
                break;
            case 'hf_materiel_badm_liste_index':
                $items = $this->buildListeBreadcrumb();
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
            ->add('MATERIEL', null, [], $this->materielSubmenu(), 'fas fa-cogs')
            ->add('Badm', null, [], $this->badmSubmenu(), 'fas fa-folder')
        ;
    }

    private function buildFirstFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création badm - Étape 1")
            ->setBackRoute('home_index')
            ->get();
    }

    private function buildSecondFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création badm - Étape 2")
            ->setBackRoute('hf_materiel_badm_first_form_index')
            ->get();
    }

    private function buildListeBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Liste de consultation du badm")
            ->setBackRoute('home_index')
            ->get();
    }
}
