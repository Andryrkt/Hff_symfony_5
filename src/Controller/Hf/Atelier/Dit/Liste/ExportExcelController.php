<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/hf/atelier/dit")
 */
class ExportExcelController extends AbstractController
{
    /**
     * @Route("/export-excel", name="hf_atelier_dit_export_excel_index")
     */
    public function index()
    {
        return $this->render('hf/atelier/dit/liste/export_excel.html.twig');
    }
}
