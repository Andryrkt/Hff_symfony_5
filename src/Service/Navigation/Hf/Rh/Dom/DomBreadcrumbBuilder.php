<?php

namespace App\Service\Navigation\Hf\Rh\Dom;

use App\Service\Navigation\BreadcrumbBuilder;
use App\Service\Navigation\Hf\BaseBreadcrumbBuilder;
use App\Contract\Navigation\BreadcrumbBuilderInterface;
use App\Repository\Admin\ApplicationGroupe\VignetteRepository;
use Symfony\Component\Security\Core\Security;

class DomBreadcrumbBuilder extends BaseBreadcrumbBuilder implements BreadcrumbBuilderInterface
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
        return in_array($context, ['dom_first_form', 'dom_second_form', 'liste_dom_index']);
    }

    public function build(array $parameters = []): array
    {
        $context = $parameters['context'] ?? 'liste_dom_index';

        switch ($context) {
            case 'dom_first_form':
                return $this->buildFirstFormBreadcrumb();
            case 'dom_second_form':
                return $this->buildSecondFormBreadcrumb();
            case 'liste_dom_index':
                return $this->buildListeBreadcrumb();
            default:
                throw new \InvalidArgumentException("Unsupported product context: {$context}");
        }
    }

    private function buildBaseBreadcrumb(): BreadcrumbBuilder
    {

        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'app_home', [], $this->accueilSubmenu())
            ->add('RH', null, [], $this->rhSubmenu())
            ->add('Ordre de mission', null, [], $this->domSubmenu())
        ;
    }

    private function buildFirstFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création d'ordre de mission - Étape 1")
            ->get();
    }

    private function buildSecondFormBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Création d'ordre de mission -Étape 2")
            ->get();
    }

    private function buildListeBreadcrumb(): array
    {
        return $this->buildBaseBreadcrumb()
            ->add("Liste de consultation de demande d'ordre de mission")
            ->get();
    }
}
