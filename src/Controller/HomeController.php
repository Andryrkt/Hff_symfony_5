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
            // Vérifier si la route existe avant de la générer
            try {
                $url = $urlGenerator->generate($link['route'], $link['params']);
            } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
                // Gérer le cas où la route n'existe pas
                // On peut retourner une URL par défaut, ou un #
                $url = '#'; 
            }

            return [
                'label' => $link['label'],
                'url' => $url,
            ];
        }, $card->getLinks());

        return $this->json([
            'title' => $card->getTitle(),
            'links' => $links,
        ]);
    }
}