<?php

namespace App\Controller\Rh\Dom;

use Symfony\Component\Routing\Annotation\Route;


use DateTime;
use App\Entity\Rh\Dom\Dom;
use App\Dto\Rh\Dom\FirstFormDto;
use App\Dto\Rh\Dom\SecondFormDto;
use App\Form\Rh\Dom\SecondFormType;
use App\Repository\Rh\Dom\DomRepository;
use App\Factory\Rh\Dom\SecondFormDtoFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\Admin\AgenceService\AgenceRepository;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use App\Service\Utils\ExtractorStringService;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



/**
 * @Route("/rh/ordre-de-mission")
 */
class DomSecondController extends AbstractController
{
    private const CODE_APPLICATION = 'DOM';

    private $secondFormDtoFactory;
    private $domRepository;

    public function __construct(SecondFormDtoFactory $firstFormDtoFactory, DomRepository $domRepository)
    {
        $this->secondFormDtoFactory = $firstFormDtoFactory;
        $this->domRepository = $domRepository;
    }

    /**
     * @Route("/dom-second-form", name="dom_second_form")
     */
    public function secondForm(
        Request $request,
        AgenceRepository $agenceRepository,
        SerializerInterface $serializer,
        NumeroGeneratorService $numeroGeneratorService,
        StatutDemandeRepository $statutDemandeRepository,
        ExtractorStringService $extractorStringService
    ) {
        // 1. gerer l'accés 
        $this->denyAccessUnlessGranted('RH_ORDRE_MISSION_CREATE');

        // recuperation de session 
        $session = $request->getSession();

        // 2. recupération des donées du premier formulaire
        $firstFormDto = $this->recuperationDonnerPremierFormulaire($session);

        // 3 . initialisation de la FirstFormDto
        $secondFormDto = $this->secondFormDtoFactory->create($firstFormDto);

        // 4. creation du formulaire
        $form = $this->createForm(SecondFormType::class, $secondFormDto);

        // 5. traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SecondFormDto $secondFormDto */
            $secondFormDto = $form->getData();
            dd($secondFormDto);
            $dom = $this->createDomFromDtos($secondFormDto, $numeroGeneratorService, $statutDemandeRepository, $extractorStringService);

            $this->domRepository->add($dom, true);

            $this->addFlash('success', 'La demande d\'ordre de mission a été créée avec succès.');

            return $this->redirectToRoute('dom_first_form');
        }

        // 6. rendu de toutes les agences pendant le premier chargement
        $agencesJson = $this->serealisationAgence($agenceRepository, $serializer);

        // rendu du vue
        return $this->render('rh/dom/secondForm.html.twig', [
            'form'          => $form->createView(),
            'secondFormDto' => $secondFormDto,
            'agencesJson' => $agencesJson,
        ]);
    }

    private function createDomFromDtos(
        SecondFormDto $secondFormDto,
        NumeroGeneratorService $numeroGeneratorService,
        StatutDemandeRepository $statutDemandeRepository,
        ExtractorStringService $extractorStringService
    ): Dom {
        $dom = new Dom();
        $user = $this->getUser();
        $statut = $statutDemandeRepository->findOneBy(['codeApplication' => self::CODE_APPLICATION, 'description' => 'OUVERT']);
        $numTel = $secondFormDto->modePayement == "MOBILE MONEY" ? $secondFormDto->mode : null;
        $codeAgenceEmetteur = $extractorStringService->extraireCode($secondFormDto->agenceUser, ' ');
        $libelleAgenceEmetteur = $extractorStringService->extraireDescription($secondFormDto->agenceUser, ' ');
        $codeSeviceEmetteur = $extractorStringService->extraireCode($secondFormDto->serviceUser, ' ');
        $libelleServiceEmetteur = $extractorStringService->extraireDescription($secondFormDto->serviceUser, ' ');

        $dom->setNumeroOrdreMission($numeroGeneratorService->autoGenerateNumero(self::CODE_APPLICATION, true));
        $dom->setMatricule($secondFormDto->matricule);
        $dom->setNomSessionUtilisateur($user->getUserIdentifier());
        //Date debut et fin mission / et nombre de jour
        $dom->setDateDebut($secondFormDto->dateHeureMission['debut']);
        $dom->setHeureDebut($secondFormDto->dateHeureMission['heureDebut']);
        $dom->setDateFin($secondFormDto->dateHeureMission['fin']);
        $dom->setHeureFin($secondFormDto->dateHeureMission['heureFin']);
        $dom->setNombreJour($secondFormDto->nombreJour);

        //
        $dom->setMotifDeplacement($secondFormDto->motifDeplacement);
        $dom->setClient($secondFormDto->client);
        $dom->setLieuIntervention($secondFormDto->lieuIntervention);
        $dom->setVehiculeSociete($secondFormDto->vehiculeSociete);
        $dom->setIndemniteForfaitaire($secondFormDto->indemniteForfaitaire);
        $dom->setTotalIndemniteForfaitaire($secondFormDto->totalIndemniteForfaitaire);
        // Autres depenses
        $dom->setAutresDepense1($secondFormDto->autresDepense1);
        $dom->setMotifAutresDepense2($secondFormDto->motifAutresDepense2);
        $dom->setAutresDepense2($secondFormDto->autresDepense2);
        $dom->setMotifAutresDepense3($secondFormDto->motifAutresDepense3);
        $dom->setAutresDepense3($secondFormDto->autresDepense3);
        $dom->setTotalAutresDepenses($secondFormDto->totalAutresDepenses);
        // total generale et mode de paiement et devis
        $dom->setTotalGeneralPayer($secondFormDto->totalGeneralPayer);
        $dom->setModePayement($secondFormDto->modePayement . ':' . $numTel);
        $dom->setDevis($secondFormDto->devis);
        //pieces joint
        $dom->setPieceJoint01($secondFormDto->pieceJoint01);
        $dom->setPieceJoint02($secondFormDto->pieceJoint02);
        // code statut , num tel, nom, prenom
        $dom->setCodeStatut($statut->getCodeStatut());
        $dom->setNumeroTel($numTel);
        $dom->setNom($secondFormDto->nom);
        $dom->setPrenom($secondFormDto->prenom);
        // agence et service
        $dom->setLibelleCodeAgenceService($libelleAgenceEmetteur . '-' . $libelleServiceEmetteur);
        //fichet et num vehicule
        $dom->setFiche($secondFormDto->fiche);
        $dom->setNumVehicule($secondFormDto->numVehicule);
        $dom->setDroitIndemnite($secondFormDto->supplementJournaliere);
        //
        $dom->setIdemnityDepl($secondFormDto->idemnityDepl);
        $dom->setDateDemande($secondFormDto->dateDemande);
        $dom->setPieceJustificatif($secondFormDto->pieceJustificatif);
        $dom->setIdStatutDemande($statut);

        // type mission, categorie et site
        $dom->setSousTypeDocument($secondFormDto->typeMission);
        $dom->setCategoryId($secondFormDto->categorie);
        $dom->setCategorie($secondFormDto->categorie->getDescription());
        $dom->setSiteId($secondFormDto->site);
        $dom->setSite($secondFormDto->site->getNomZone());


        return $dom;
    }

    private function recuperationDonnerPremierFormulaire(SessionInterface $session)
    {
        /** @var FirstFormDto $firstFormDto */
        $firstFormDto = $session->get('dom_first_form_data');
        if (!$firstFormDto) {
            // Handle case where first form data is not in session, e.g., redirect to first form
            return $this->redirectToRoute('dom_first_form');
        }

        return $firstFormDto;
    }

    private function serealisationAgence(AgenceRepository $agenceRepository, SerializerInterface $serializer)
    {
        // 1. Récupérer toutes les agences avec leurs services
        $agences = $agenceRepository->findAll();

        // 2. Sérialiser les données en JSON en utilisant les groupes que nous avons définis
        $agencesJson = $serializer->serialize($agences, 'json', ['groups' => 'agence:read']);

        return $agencesJson;
    }
}
