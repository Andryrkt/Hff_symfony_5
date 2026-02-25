<?php

namespace App\Controller\Hf\Atelier\Dit\Creation;

use App\Form\Hf\Atelier\Dit\DitFormType;
use App\Service\Hf\Atelier\Dit\PdfService;
use App\Factory\Hf\Atelier\Dit\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Hf\Atelier\Dit\CreationHandler;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Hf\Atelier\Dit\Creation\AbstractDitFormController;


/**
 * @Route("/hf/atelier/dit/creation")
 */
class DuplicationController extends AbstractDitFormController
{
    /**
     * @Route("/duplication/{numDit}", name="hf_atelier_dit_creation_duplication")
     */
    public function index(
        string $numDit,
        FormFactory $formFactory,
        Request $request,
        PdfService $pdfService,
        CreationHandler $creationHandler
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('ATELIER_DIT_CREATE');

        // 2. initialisation (recupération des information DIT)
        $dto = $formFactory->duplicate($numDit);

        // 3. creation du formulaire
        $form = $this->createForm(DitFormType::class, $dto);

        // 4. gerer la soumission du formulaire
        $this->traitementFormulaire($request, $form, $pdfService, $creationHandler);

        return $this->render('hf/atelier/dit/creation/duplication.html.twig', [
            'form'       => $form->createView(),
            'breadcrumbs' => $this->breadcrumbBuilder->build('hf_atelier_dit_creation_duplication'),
            'materielData' => $dto->idMateriel ? json_encode([
                'num_matricule'  => $dto->idMateriel,
                'num_parc'       => $dto->numParc,
                'num_serie'      => $dto->numSerie,
                'constructeur'   => $dto->constructeur,
                'designation'    => $dto->designation,
                'modele'         => $dto->modele,
                'marque'         => $dto->marque,
                'casier_emetteur' => $dto->casier,
                'heure'          => $dto->heureMachine,
                'km'             => $dto->kmMachine,
            ]) : null,
        ]);
    }
}
