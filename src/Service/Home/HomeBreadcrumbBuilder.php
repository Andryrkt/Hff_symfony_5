<?php

namespace App\Service\Home;

use App\Contract\Navigation\BreadcrumbBuilderInterface;
use App\Service\Navigation\BreadcrumbBuilder;

class HomeBreadcrumbBuilder implements BreadcrumbBuilderInterface
{
    private $breadcrumb;

    public function __construct(BreadcrumbBuilder $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    public function supports(string $context): bool
    {
        return in_array($context, ['home', 'app_home']);
    }

    public function build(array $parameters = []): array
    {
        return $this->breadcrumb
            ->clear()
            ->add('Accueil', 'app_home')
            ->get();
    }
}
