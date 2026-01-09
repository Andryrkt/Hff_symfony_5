<?php

namespace App\Controller\Hf\Materiel\Badm\Liste;

use App\Mapper\Hf\Materiel\Badm\BadmMapper;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class DetailController extends AbstractController
{

    /**
     * @Route("/detail/{numeroBadm}", name="hf_materiel_badm_detail_index")
     */
    public function index(
        string $numeroBadm,
        BadmRepository $badmRepository,
        BadmMapper $badmMapper
    ) {
        $badm = $badmRepository->findOneBy(['numeroBadm' => $numeroBadm]);
        $secondFormDto = $badmMapper->reverseMap($badm);

        return $this->render('hf/materiel/badm/liste/detail.html.twig', [
            'secondFormDto' => $secondFormDto
        ]);
    }
}
