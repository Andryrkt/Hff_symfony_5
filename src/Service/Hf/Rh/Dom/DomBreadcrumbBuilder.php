<?php

namespace App\Service\Hf\Rh\Dom;

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
        return in_array($context, ['dom_first_form', 'dom_second_form']);
    }

    public function build(array $parameters = []): array
    {
        $this->breadcrumb
            ->clear()
            ->add('Accueil', 'app_home')
            ->add('Ordre de Mission', 'dom_first_form');

        if ($parameters['context'] === 'dom_second_form') {
            $this->breadcrumb->add('Étape 2');
        }

        return $this->breadcrumb->get();
    }
}
