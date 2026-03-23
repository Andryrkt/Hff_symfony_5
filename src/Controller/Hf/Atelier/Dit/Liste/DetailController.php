<?php

namespace App\Controller\Hf\Atelier\Dit\Liste;

use App\Mapper\Hf\Atelier\Dit\Mapper;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/***
 * @Route("/hf/atelier/dit/liste")
 */
class DetailController extends AbstractController
{
    /**
     * @Route("/detail/{numDit}", name="hf_atelier_dit_liste_detail")
     */
    public function index(
        string $numDit,
        DitRepository $ditRepository,
        Mapper $ditMapper
    ) {
        $dit = $ditRepository->findOneBy(['numeroDit' => $numDit]);
        $dto = $ditMapper->reverseMap($dit);
        return $this->render('hf/atelier/dit/liste/detail.html.twig', [
            'dto' => $dto,
        ]);
    }
}
