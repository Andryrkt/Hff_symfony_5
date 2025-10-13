<?php

namespace App\Service\navigation;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreadcrumbBuilderService
{
    private array $items = [];
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function add(string $label, ?string $route = null, array $params = [], array $submenu = []): self
    {
        $url = $route ? $this->urlGenerator->generate($route, $params) : null;

        $this->items[] = [
            'label' => $label,
            'url' => $url,
            'submenu' => $submenu, // sous-menu optionnel
        ];

        return $this;
    }

    public function get(): array
    {
        return $this->items;
    }
}
