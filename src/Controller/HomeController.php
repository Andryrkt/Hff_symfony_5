<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Navigation\Home\HomeCardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ContextAwareBreadcrumbBuilder $breadcrumbBuilder, HomeCardService $homeCardService): Response
    {
        $breadcrumb = $breadcrumbBuilder->build('home');

        return $this->render('home/index.html.twig', [
            'breadcrumb' => $breadcrumb,
            'cards' => $homeCardService->getHomeCards(),
        ]);
    }

    /**
     * @Route("/api/home/card/{id}", name="api_home_card", methods={"GET"})
     */
    public function getCardContent(int $id, HomeCardService $homeCardService, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $card = $homeCardService->getCardByIndex($id);

        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }

        $links = $this->generateLinkUrls($card->getLinks(), $urlGenerator);

        return $this->json([
            'title' => $card->getTitle(),
            'links' => $links,
        ]);
    }

    /**
     * Génère récursivement les URLs pour une structure de liens hiérarchique.
     */
    private function generateLinkUrls(array $links, UrlGeneratorInterface $urlGenerator): array
    {
        return array_map(function ($link) use ($urlGenerator) {
            // S'assure que les clés de base existent
            $pathOrRoute = $link['route'] ?? null;
            $params = $link['params'] ?? [];
            $link['newTab'] = $link['newTab'] ?? false;
            $link['children'] = $link['children'] ?? [];
            $link['icon'] = $link['icon'] ?? null;
            $url = '#'; // URL par défaut

            if (is_string($pathOrRoute)) {
                if (str_starts_with($pathOrRoute, 'http')) {
                    $url = $pathOrRoute;
                } elseif (str_starts_with($pathOrRoute, '/')) {
                    $url = $pathOrRoute;
                } else {
                    try {
                        $url = $urlGenerator->generate($pathOrRoute, $params);
                    } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                        $url = '#'; // La route n'existe pas
                    }
                }
            }

            // Assigne l'URL générée
            $link['url'] = $url;

            // Traite les enfants récursivement
            if (!empty($link['children'])) {
                $link['children'] = $this->generateLinkUrls($link['children'], $urlGenerator);
            }
            
            // Retourne uniquement les clés nécessaires pour le frontend
            return [
                'label' => $link['label'],
                'url' => $link['url'],
                'newTab' => $link['newTab'],
                'children' => $link['children'],
                'icon' => $link['icon'],
            ];

        }, $links);
    }
}