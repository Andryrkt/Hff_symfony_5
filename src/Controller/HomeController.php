<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Navigation\Home\HomeCardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function getCardContent(int $id, HomeCardService $homeCardService): JsonResponse
    {
        $card = $homeCardService->getCardByIndex($id);

        if (!$card) {
            return $this->json(['error' => 'Card not found'], 404);
        }

        return $this->json([
            'title' => $card->getTitle(),
            'links' => $card->getLinks(),
        ]);
    }
}
