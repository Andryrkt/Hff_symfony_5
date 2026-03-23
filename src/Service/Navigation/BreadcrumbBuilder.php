<?php

namespace App\Service\Navigation;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreadcrumbBuilder
{
    private array $items = [];
    private UrlGeneratorInterface $urlGenerator;
    private ?string $backRoute = null;
    private array $backRouteParams = [];

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function add(string $label, ?string $route = null, array $params = [], array $submenu = [], ?string $icon = null): self
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
            'icon' => $icon,
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
            $icon = $item['icon'] ?? null;
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
                'icon' => $icon,
                'submenu' => $this->processSubmenu($nestedSubmenu), // Appel récursif
            ];
        }
        return $processed;
    }

    public function setBackRoute(string $route, array $params = []): self
    {
        $this->backRoute = $route;
        $this->backRouteParams = $params;

        return $this;
    }

    public function getBackConfig(): array
    {
        return [
            'back_route' => $this->backRoute,
            'back_route_params' => $this->backRouteParams,
        ];
    }

    public function get(): array
    {
        return $this->items;
    }

    public function clear(): self
    {
        $this->items = [];
        $this->backRoute = null;
        $this->backRouteParams = [];

        return $this;
    }
}
