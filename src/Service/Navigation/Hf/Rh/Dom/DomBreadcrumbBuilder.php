<?php

namespace App\Service\Navigation\Hf\Rh\Dom;

use App\Contract\Navigation\BreadcrumbBuilderInterface;
use App\Service\Navigation\BreadcrumbBuilder;

class DomBreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    private $breadcrumb;

    public function __construct(BreadcrumbBuilder $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function supports(string $context): bool
    {
        return in_array($context, ['dom_first_form', 'dom_second_form', 'dom_liste']);
    }

    public function build(array $parameters = []): array
    {
        $context = $parameters['context'] ?? 'dom_liste';

        switch ($context) {
            case 'dom_first_form':
                return $this->buildFirstFormBreadcrumb();
            case 'dom_second_form':
                return $this->buildSecondFormBreadcrumb();
            case 'dom_liste':
                return $this->buildListeBreadcrumb();
            default:
                throw new \InvalidArgumentException("Unsupported product context: {$context}");
        }
    }

    private function buildFirstFormBreadcrumb(): array
    {
        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'app_home')
            ->add('Ordre de Mission', 'dom_first_form')
            ->add('Dom - Étape 1')
            ->get();
    }

    private function buildSecondFormBreadcrumb(): array
    {
        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'app_home')
            ->add('Ordre de Mission', 'dom_first_form')
            ->add('Dom -Étape 2')
            ->get();
    }

    private function buildListeBreadcrumb(): array
    {
        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'app_home')
            ->add('Ordre de Mission', 'dom_liste')
            ->get();
    }
}
