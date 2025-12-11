<?php

namespace App\Controller\Hf\Rh\Dom\Liste;


use App\Repository\Hf\Rh\Dom\DomRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/rh/ordre-de-mission")
 */
class DomDetailController extends AbstractController
{
    /**
     * @Route("/detail/{numeroOrdreMission}", name="dom_detail")
     */
    public function detailDom(string $numeroOrdreMission, DomRepository $domRepository)
    {

        $dom = $domRepository->findOneBy(['numeroOrdreMission' => $numeroOrdreMission]);
        $dom->setIdemnityDepl((int)str_replace('.', '', $dom->getIdemnityDepl()));
        $salarier  = strlen($dom->getMatricule()) === 4 && ctype_digit($dom->getMatricule()) ? 'PERMANENT' :  'TEMPORAIRE';


        return $this->render(
            'hf/rh/dom/liste/detail.html.twig',
            [
                'dom' => $dom,
                'salarier' => $salarier
            ]
        );
    }
}
