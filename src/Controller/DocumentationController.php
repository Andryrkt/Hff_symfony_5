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
            // Attempt to find the file recursively by basename
            $baseName = basename($page);
            if (substr($baseName, -3) !== '.md') {
                $baseName .= '.md';
            }

            $foundPath = null;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->docsDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getBasename() === $baseName) {
                    $foundPath = $file->getPathname();
                    // Update page variable for correct relative link resolution later
                    $relativePath = substr($foundPath, strlen($this->docsDir) + 1);
                    $page = str_replace(['.md', '\\'], ['', '/'], $relativePath);
                    break;
                }
            }

            if ($foundPath) {
                $filePath = $foundPath;
            } else {
                throw new NotFoundHttpException('Documentation page not found: ' . $page);
            }
        }

        $content = file_get_contents($filePath);
        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($content);

        // Rewrite links: generate absolute URLs using Symfony router
        $htmlContent = preg_replace_callback('/href="((?!http|#|mailto|ftp)[^"]+)\.md"/', function ($matches) use ($page) {
            $link = $matches[1];

            // Handle absolute paths (from docs root) starting with /
            if (strpos($link, '/') === 0) {
                return 'href="' . $this->generateUrl('app_documentation_index', ['page' => ltrim($link, '/')]) . '"';
            }

            // Handle relative paths
            $currentDir = dirname($page);
            if ($currentDir === '.') {
                $targetPath = $link;
            } else {
                $targetPath = $currentDir . '/' . $link;
            }

            // Canonicalize path (resolve .. and .)
            $parts = explode('/', str_replace('\\', '/', $targetPath));
            $canonicalParts = [];
            foreach ($parts as $part) {
                if ($part === '..') {
                    array_pop($canonicalParts);
                } elseif ($part !== '.' && $part !== '') {
                    $canonicalParts[] = $part;
                }
            }
            $finalPage = implode('/', $canonicalParts);

            return 'href="' . $this->generateUrl('app_documentation_index', ['page' => $finalPage]) . '"';
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
        $menu = [];
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->docsDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'md') {
                $relativePath = substr($file->getPathname(), strlen($this->docsDir) + 1);
                $slug = str_replace(['.md', '\\'], ['', '/'], $relativePath);
                $basename = $file->getBasename('.md');

                // Determine Category (Folder)
                $parts = explode('/', $slug);
                // If it's a file in a subdirectory
                if (count($parts) > 1) {
                    $categorySlug = $parts[0];
                    $categoryTitle = ucfirst(str_replace(['_', '-'], ' ', $categorySlug));
                    $title = ucfirst(str_replace(['_', '-'], ' ', $basename));

                    if (!isset($menu[$categoryTitle])) {
                        $slugger = new AsciiSlugger();
                        $menu[$categoryTitle] = [
                            'type' => 'category',
                            'title' => $categoryTitle,
                            'id' => strtolower($slugger->slug($categoryTitle))
                        ];
                        $menu[$categoryTitle]['items'] = [];
                    }
                    $menu[$categoryTitle]['items'][] = ['slug' => $slug, 'title' => $title];
                } else {
                    // Root file
                    $title = ucfirst(str_replace(['_', '-'], ' ', $basename));
                    if ($basename === 'README') {
                        // Ensure README is always at the very top level special item
                        $menu['__ROOT__README__'] = ['type' => 'link', 'slug' => 'README', 'title' => 'Accueil'];
                    } else {
                        // Other root files
                        if (!isset($menu['Général'])) {
                            $slugger = new AsciiSlugger();
                            $menu['Général'] = [
                                'type' => 'category',
                                'title' => 'Général',
                                'id' => 'general',
                                'items' => []
                            ];
                        }
                        $menu['Général']['items'][] = ['slug' => $slug, 'title' => $title];
                    }
                }
            }
        }

        // Sort Categories
        ksort($menu);

        // Sort items within categories
        foreach ($menu as &$entry) {
            if (isset($entry['items'])) {
                usort($entry['items'], function ($a, $b) {
                    return strcasecmp($a['title'], $b['title']);
                });
            }
        }

        // Move README to top if exists and flatten/arrange for simpler Twig usage? 
        // Or keep structure: ['CategoryName' => ['items' => [...]], 'LinkKey' => ['type' => 'link', ...]]
        // Let's ensure 'Accueil' is first.
        $finalMenu = [];
        if (isset($menu['__ROOT__README__'])) {
            $finalMenu[] = $menu['__ROOT__README__'];
            unset($menu['__ROOT__README__']);
        }

        // Add other categories
        foreach ($menu as $key => $value) {
            if (isset($value['type']) && $value['type'] === 'category') {
                $finalMenu[] = [
                    'type' => 'category',
                    'title' => $key,
                    'items' => $value['items']
                ];
            } else {
                $finalMenu[] = $value;
            }
        }

        return $finalMenu;
    }
}
