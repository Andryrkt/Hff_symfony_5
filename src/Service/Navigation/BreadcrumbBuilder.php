<?php

namespace App\Service\Navigation;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreadcrumbBuilder
{
    private array $items = [];
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function add(string $label, ?string $route = null, array $params = [], array $submenu = []): self
    {
        $url = null;
        if ($route === '#') {
            $url = '#';
        } elseif ($route) {
            $url = $this->urlGenerator->generate($route, $params);
        }

        $this->items[] = [
            'label' => $label,
            'url' => $url,
            'submenu' => $this->processSubmenu($submenu), // Traiter les sous-menus récursivement
        ];

        return $this;
    }

    private function processSubmenu(array $submenu): array
    {
        $processed = [];
        foreach ($submenu as $item) {
            $route = $item['route'] ?? null;
            $params = $item['params'] ?? [];
            $nestedSubmenu = $item['submenu'] ?? [];

            $url = null;
            if ($route === '#') {
                $url = '#';
            } elseif ($route) {
                $url = $this->urlGenerator->generate($route, $params);
            }
            
            $processed[] = [
                'label' => $item['label'],
                'url' => $url,
                'submenu' => $this->processSubmenu($nestedSubmenu), // Appel récursif
            ];
        }
        return $processed;
    }

    public function get(): array
    {
        return $this->items;
    }

    public function clear(): self
    {
        $this->items = [];

        return $this;
    }
}