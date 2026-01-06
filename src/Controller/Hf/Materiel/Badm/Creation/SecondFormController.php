<?php

namespace App\Controller\Hf\Materiel\Badm\Creation;

use App\Model\Hf\Materiel\Badm\BadmModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\Hf\Materiel\Badm\SecondFormFactory;
use App\Form\Hf\Materiel\Badm\Creation\SecondFormType;
use App\Service\Navigation\ContextAwareBreadcrumbBuilder;
use App\Service\Hf\Materiel\Badm\BadmBlockingConditionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/badm")
 */
class SecondFormController extends AbstractController
{
    /**
     * @Route("/second-form", name="hf_materiel_badm_second_form_index")
     */
    public function index(
        Request $request,
        BadmModel $badmModel,
        BadmBlockingConditionService $badmBlockingConditionService,
        SecondFormFactory $secondFormFactory,
        ContextAwareBreadcrumbBuilder $breadcrumbBuilder
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_BADM_CREATE');

        // 2. Récupération de l'information envoyer par le premier formulaire dans la session 
        $firstFormDto = $this->getFirstFormDataFromSession($request);

        // 3. Récupération des infos matériel depuis IPS
        $infoMaterielDansIps = $badmModel->getInfoMateriel($firstFormDto);

        // 4. CONDITION DE BLOCAGE 
        $blockingMessage = !$this->isGranted('ROLE_ADMIN') ? $badmBlockingConditionService->checkBlockingConditions($firstFormDto, $infoMaterielDansIps) : null;
        if ($blockingMessage) {
            $this->addFlash('warning', $blockingMessage);
            return $this->redirectToRoute('hf_materiel_badm_first_form_index');
        }

        // 5. Initialisation du secondFormDto
        $secondFormDto = $secondFormFactory->create($firstFormDto, $infoMaterielDansIps);

        // 6. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // TODO: 7. traitement du formulaire
        // $this->traitementFormulaire($request, $form);

        return $this->render('hf/materiel/badm/creation/second_form.html.twig', [
            'form' => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'breadcrumbs' => $breadcrumbBuilder->build('hf_materiel_badm_second_form_index'),
        ]);
    }

    /**
     * Recupération de l'information envoyer par le premier formulaire dans la session
     * 
     * return FirstFormDto|RedirectResponse
     */
    private function getFirstFormDataFromSession(Request $request)
    {
        $firstFormDto = $request->getSession()->get('badm_first_form_data');
        if (!$firstFormDto) {
            $this->addFlash('warning', 'La session a expiré, veuillez recommencer.');
            return $this->redirectToRoute('hf_materiel_badm_first_form_index');
        }

        return $firstFormDto;
    }
}
