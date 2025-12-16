<?php

namespace App\Controller;

use Parsedown;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @Route("/documentation")
 */
class DocumentationController extends AbstractController
{
    private string $docsDir;

    public function __construct(string $projectDir)
    {
        $this->docsDir = $projectDir . '/docs';
    }

    /**
     * @Route("/{page}", name="app_documentation_index", requirements={"page"=".+"}, defaults={"page"="README"})
     */
    public function index(string $page): Response
    {
        // Sanitize path to prevent directory traversal
        if (strpos($page, '..') !== false) {
            throw new NotFoundHttpException('Invalid page.');
        }

        // Special case for root README
        if ($page === 'project_readme') {
            $filePath = dirname($this->docsDir) . '/README.md';
        } else {
            // Adjust for index/readme
            if ($page === 'index') {
                $page = 'README';
            }

            $filePath = $this->docsDir . '/' . $page . '.md';

            // Try without extension if failed
            if (!file_exists($filePath)) {
                $filePath = $this->docsDir . '/' . $page;
            }
        }

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('Documentation page not found: ' . $page);
        }

        $content = file_get_contents($filePath);
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($content);

        // Rewrite links: generate absolute URLs using Symfony router
        $htmlContent = preg_replace_callback('/href="((?!http|#)[^"]+)\.md"/', function ($matches) {
            return 'href="' . $this->generateUrl('app_documentation_index', ['page' => $matches[1]]) . '"';
        }, $htmlContent);

        // Get list of available docs for sidebar
        $menu = $this->getDocMenu();

        if ($this->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $htmlContent,
                'title' => $page
            ]);
        }

        return $this->render('documentation/index.html.twig', [
            'content' => $htmlContent,
            'current_page' => $page,
            'menu' => $menu
        ]);
    }

    private function isXmlHttpRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    private function getDocMenu(): array
    {
        $files = glob($this->docsDir . '/*.md');
        $menu = [];
        $slugger = new AsciiSlugger();

        foreach ($files as $file) {
            $basename = basename($file, '.md');
            $title = ucfirst(str_replace(['_', '-'], ' ', $basename));

            // Prioritize README
            if ($basename === 'README') {
                array_unshift($menu, ['slug' => 'README', 'title' => 'Accueil']);
            } else {
                $menu[] = ['slug' => $basename, 'title' => $title];
            }
        }

        return $menu;
    }
}
