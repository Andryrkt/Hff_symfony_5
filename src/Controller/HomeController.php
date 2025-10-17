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

        $links = array_map(function ($link) use ($urlGenerator) {
            $pathOrRoute = $link['route'];
            $params = $link['params'];
            $url = '#'; // URL par défaut si rien ne correspond

            if (is_string($pathOrRoute)) {
                // Case 1: URL externe (commence par http:// ou https://)
                if (str_starts_with($pathOrRoute, 'http')) {
                    $url = $pathOrRoute;
                }
                // Case 2: Chemin interne absolu (commence par /)
                else if (str_starts_with($pathOrRoute, '/')) {
                    $url = $pathOrRoute;
                }
                // Case 3: Nom de route Symfony
                else {
                    try {
                        $url = $urlGenerator->generate($pathOrRoute, $params);
                    } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                        // La route n'a pas été trouvée, l'URL reste '#'
                        $url = "#";
                    }
                }
            }

            return [
                'label' => $link['label'],
                'url' => $url,
                'newTab' => $link['newTab'] ?? false, // Inclure l'info newTab
            ];
        }, $card->getLinks());

        return $this->json([
            'title' => $card->getTitle(),
            'links' => $links,
        ]);
    }
}
