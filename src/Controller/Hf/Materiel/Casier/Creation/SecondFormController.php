<?php

namespace App\Controller\Hf\Materiel\Casier\Creation;

use App\Model\Hf\Materiel\Casier\CasierModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\Hf\Materiel\Casier\SecondFormFactory;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/hf/materiel/casier")
 */
class SecondFormController extends AbstractController
{
    /**
     * @Route("/second-form", name="hf_materiel_casier_second_form_index")
     */
    public function index(Request $request, CasierModel $casierModel, SecondFormFactory $secondFormFactory)
    {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('MATERIEL_CASIER_CREATE');

        // 2.Recupération de l'information envoyer par le premier formulaire dans la session 
        $firstFormDto = $this->getFirstFormDataFromSession($request->getSession());

        // 3. Récupération de l'information sur le matériel
        $caracteristiqueMateriel = $casierModel->getCaracteristiqueMateriel($firstFormDto);

        // 4. Initialisation du secondFormDto
        $secondFormDto = $secondFormFactory->create($caracteristiqueMateriel);

        // 5. creation du formulaire

        return $this->render('hf/materiel/casier/creation/second_form.html.twig');
    }

    /**
     * Recupération de l'information envoyer par le premier formulaire dans la session
     * 
     * return FirstFormDto|RedirectResponse
     */
    private function getFirstFormDataFromSession(SessionInterface $session)
    {
        $firstFormDto = $session->get('casier_first_form_data');
        if (!$firstFormDto) {
            $this->addFlash('warning', 'La session a expiré, veuillez recommencer.');
            return $this->redirectToRoute('hf_materiel_casier_first_form_index');
        }

        return $firstFormDto;
    }
}
