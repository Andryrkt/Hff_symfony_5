<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Parsedown;

class DocumentationController extends AbstractController
{
    private $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
    }

    /** 
     * @Route("/admin/documentation/voters", name="app_documentation_voters")] 
     * 
    */
    public function voters(): Response
    {
        $markdownFilePath = $this->projectDir . '/Documentation/Voters.md';

        if (!file_exists($markdownFilePath)) {
            throw $this->createNotFoundException('Le fichier de documentation n\'a pas été trouvé.');
        }

        $markdownContent = file_get_contents($markdownFilePath);

        $parsedown = new Parsedown();
        $htmlContent = $parsedown->text($markdownContent);

        return $this->render('documentation/voters.html.twig', [
            'htmlContent' => $htmlContent,
        ]);
    }
}
