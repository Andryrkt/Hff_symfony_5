<?php

namespace App\Service\Navigation\Hf\Rh\Dom;

use App\Service\Navigation\BreadcrumbBuilder;
use App\Service\Navigation\Hf\BaseBreadcrumbBuilder;
use App\Contract\Navigation\BreadcrumbBuilderInterface;
use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use Symfony\Component\Security\Core\Security;

final class DomBreadcrumbBuilder extends BaseBreadcrumbBuilder implements BreadcrumbBuilderInterface
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
            'dom_first_form',
            'dom_second_form',
            'liste_dom_index',
            'dom_duplication'
        ]);
    }

    public function build(array $parameters = []): array
    {
        $context = $parameters['context'] ?? 'liste_dom_index';

        $items = [];
        $backConfig = [];

        switch ($context) {
            case 'dom_first_form':
                $items = $this->buildFirstFormBreadcrumb();
                break;
            case 'dom_second_form':
                $items = $this->buildSecondFormBreadcrumb();
                break;
            case 'liste_dom_index':
                $items = $this->buildListeBreadcrumb();
                break;
            case 'dom_duplication':
                $items = $this->buildDuplicationBreadcrumb($parameters['numeroOrdreMission'] ?? null);
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
            ->add('Accueil', 'app_home')
            ->add('HFF', null, [], $this->hfSubmenu())
            ->add('RH', null, [], $this->rhSubmenu())
            ->add('Ordre de mission', null, [], $this->domSubmenu())
        ;
    }

    private function buildFirstFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création d'ordre de mission - Étape 1")
            ->setBackRoute('app_home')
            ->get();
    }

    private function buildSecondFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création d'ordre de mission -Étape 2")
            ->setBackRoute('dom_first_form')
            ->get();
    }

    private function buildListeBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Liste de consultation de demande d'ordre de mission")
            ->setBackRoute('app_home')
            ->get();
    }

    private function buildDuplicationBreadcrumb($numeroOrdreMission): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Duplication d'ordre de mission n° {$numeroOrdreMission}")
            ->setBackRoute('liste_dom_index')
            ->get();
    }
}
