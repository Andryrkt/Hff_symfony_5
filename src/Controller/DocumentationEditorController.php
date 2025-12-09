<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Parsedown;

class DocumentationEditorController extends AbstractController
{
    private $projectDir;
    private $filesystem;
    private $parsedown;

    public function __construct(KernelInterface $kernel, Filesystem $filesystem, Parsedown $parsedown)
    {
        $this->projectDir = $kernel->getProjectDir();
        $this->filesystem = $filesystem;
        $this->parsedown = $parsedown;
    }

    /**
     * @Route("/admin/documentation/editor", name="app_documentation_editor_list")
     */
    public function list(): Response
    {
        $documentationDir = $this->projectDir . '/Documentation';
        $files = [];

        if (is_dir($documentationDir)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($documentationDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'md') {
                    $relativePath = str_replace($documentationDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $files[] = $relativePath;
                }
            }
        }

        return $this->render('documentation_editor/list.html.twig', [
            'files' => $files,
        ]);
    }

    /**
     * @Route("/admin/documentation/editor/edit/{filepath}", name="app_documentation_editor_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, string $filepath): Response
    {
        $decodedFilepath = base64_decode($filepath);
        $absoluteFilepath = $this->projectDir . '/Documentation/' . $decodedFilepath;

        if (!$this->filesystem->exists($absoluteFilepath)) {
            throw $this->createNotFoundException('Le fichier de documentation n\'a pas été trouvé.');
        }

        $content = file_get_contents($absoluteFilepath);

        if ($request->isMethod('POST')) {
            $newContent = $request->request->get('content');
            $this->filesystem->dumpFile($absoluteFilepath, $newContent);
            $this->addFlash('success', 'Documentation mise à jour avec succès.');

            return $this->redirectToRoute('app_documentation_editor_list');
        }

        return $this->render('documentation_editor/edit.html.twig', [
            'filepath' => $decodedFilepath,
            'content' => $content,
        ]);
    }

    /**
     * @Route("/admin/documentation/editor/new", name="app_documentation_editor_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $filename = $request->request->get('filename');
            $folder = $request->request->get('folder');
            $content = $request->request->get('content');

            if (empty($filename)) {
                $this->addFlash('error', 'Le nom du fichier ne peut pas être vide.');
                return $this->redirectToRoute('app_documentation_editor_new');
            }

            $targetDirectory = $this->projectDir . '/Documentation/';
            if (!empty($folder)) {
                $targetDirectory .= trim($folder, '/\\ ') . DIRECTORY_SEPARATOR;
            }

            $absoluteFilepath = $targetDirectory . $filename;

            if ($this->filesystem->exists($absoluteFilepath)) {
                $this->addFlash('error', 'Un fichier avec ce nom existe déjà.');
                return $this->redirectToRoute('app_documentation_editor_new');
            }

            // Create the directory if it doesn't exist
            $this->filesystem->mkdir(dirname($absoluteFilepath));

            $this->filesystem->dumpFile($absoluteFilepath, $content);
            $this->addFlash('success', 'Fichier de documentation créé avec succès.');

            return $this->redirectToRoute('app_documentation_editor_list');
        }

        return $this->render('documentation_editor/new.html.twig');
    }

    /**
     * @Route("/admin/documentation/editor/preview", name="app_documentation_editor_preview", methods={"POST"})
     */
    public function preview(Request $request): Response
    {
        $markdownContent = $request->request->get('markdown');
        $htmlContent = $this->parsedown->text($markdownContent);

        return new Response($htmlContent);
    }
}
