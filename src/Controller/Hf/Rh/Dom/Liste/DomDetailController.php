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
     * @Route("/detail/{id}", name="dom_detail")
     */
    public function detailDom($id, DomRepository $domRepository)
    {

        $dom = $domRepository->findOneBy(['id' => $id]);
        $dom->setIdemnityDepl((int)str_replace('.', '', $dom->getIdemnityDepl()));
        $matricule = $dom->getMatricule();
        if (strlen($matricule) === 4 && ctype_digit($matricule)) {
            $salarier = 'PERMANENT';
        } else {
            $salarier = 'TEMPORAIRE';
        }
        // dd($dom);
        return $this->render(
            'hf/rh/dom/liste/detail.html.twig',
            [
                'dom' => $dom,
                'salarier' => $salarier
            ]
        );
    }
}
