<?php

namespace App\Controller\dom;


use App\Entity\dom\Dom;
use App\Entity\admin\Agence;
use App\Entity\admin\Service;
use App\Form\dom\DomForm1Type;
use App\Entity\admin\utilisateur\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\admin\dom\SousTypeDocument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/rh/ordre-de-mission")
 */
class DomFirstController extends AbstractController
{

    /**
     * @Route("/dom-first-form", name="dom_first_form")
     */
    public function firstForm(Request $request, SessionInterface $sessionService, EntityManagerInterface $entityManager)
    {

        // Recupération de l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $dom = new Dom();
        $agenceAutoriserId = $user->getAgenceAutoriserIds();
        $codeAgences = [];
        foreach ($agenceAutoriserId as $value) {
            $codeAgences[] = $entityManager->getRepository(Agence::class)->find($value)->getCodeAgence();
        }

        $serviceAutoriserId = $user->getServiceAutoriserIds();
        $codeService = [];
        foreach ($serviceAutoriserId as $value) {
            $codeService[] = $entityManager->getRepository(Service::class)->find($value)->getCodeService();
        }

        //INITIALISATION 
        $agenceServiceIps = $this->agenceServiceIpsString();
        $dom
            ->setAgenceEmetteur($agenceServiceIps['agenceIps'])
            ->setServiceEmetteur($agenceServiceIps['serviceIps'])
            ->setSousTypeDocument($entityManager->getRepository(SousTypeDocument::class)->find(2))
            ->setSalarier('PERMANENT')
            ->setCodeAgenceAutoriser($codeAgences)
            ->setCodeServiceAutoriser($codeService)
        ;


        $form = $this->createForm(DomForm1Type::class, $dom);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $salarier = $form->get('salarie')->getData();

            $dom->setSalarier($salarier);
            $formData = $form->getData()->toArray();


            $sessionService->set('form1Data', $formData);

            // Redirection vers le second formulaire
            return $this->redirectToRoute('dom_second_form');
        }

        return $this->render('dom/doms/firstForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function autorisationRole(EntityManagerInterface $em, Security $security): bool
    {
        /** CREATION D'AUTORISATION */
        $userConnecter = $security->getUser();
        if (!$userConnecter) {
            return false;
        }
        $roleIds = $userConnecter->getRoleIds();
        return in_array(1, $roleIds) || in_array(4, $roleIds);
    }

    private function notification(SessionInterface $sessionService, $message)
    {
        $sessionService->set('notification', ['type' => 'danger', 'message' => $message]);
        return $this->redirectToRoute("dom_first_form");
    }
}
