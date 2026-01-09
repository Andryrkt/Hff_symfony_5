<?php

namespace App\Form\Hf\Materiel\Badm\Creation;

use App\Entity\Admin\AgenceService\Agence;
use App\Form\Common\FileUploadType;
use App\Service\Utils\FormattingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Form\Common\AgenceServiceCasierType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SecondFormType extends AbstractType
{
    const MODE_PAYEMENT = [
        "TRAITE" => "TRAITE",
        "CHEQUE" => "CHEQUE",
        "VIREMENT" => "VIREMENT"
    ];

    private FormattingService $formattingService;
    private EntityManagerInterface $em;

    public function __construct(
        FormattingService $formattingService,
        EntityManagerInterface $em
    ) {
        $this->formattingService = $formattingService;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typeMouvement = $options["data"]->typeMouvement->getDescription();

        $agences = $this->em->getRepository(Agence::class)->findAll();
        $agencesData = [];
        foreach ($agences as $agence) {
            $servicesData = [];
            foreach ($agence->getServices() as $service) {
                $servicesData[] = [
                    'id' => $service->getId(),
                    'code' => $service->getCode(),
                    'nom' => $service->getNom(),
                ];
            }
            $casiersData = [];
            foreach ($agence->getCasierPhps() as $casier) {
                $casiersData[] = [
                    'id' => $casier->getId(),
                    'nom' => $casier->getNom(),
                ];
            }
            $agencesData[] = [
                'id' => $agence->getId(),
                'code' => $agence->getCode(),
                'nom' => $agence->getNom(),
                'services' => $servicesData,
                'casiers' => $casiersData,
            ];
        }

        $builder
            // =============== CARACTERISTIQUES DU MATERIEL ===============
            ->add(
                'designation',
                TextType::class,
                [
                    'label' => 'Désignation ',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->designation
                ]
            )
            ->add(
                'idMateriel',
                TextType::class,
                [
                    'label' => 'ID matériel',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->idMateriel
                ]
            )
            ->add(
                'numSerie',
                TextType::class,
                [
                    'label' => 'N° Série ',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->numSerie
                ]
            )
            ->add(
                'numParc',
                TextType::class,
                [
                    'label' => 'N° Parc',
                    'disabled' => $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC,
                    'data' => $options["data"]->numParc,
                    'required' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC
                ]
            )
            ->add(
                'groupe',
                TextType::class,
                [
                    'label' => 'Groupe ',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->groupe
                ]
            )
            ->add(
                'constructeur',
                TextType::class,
                [
                    'label' => 'Constructeur',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->constructeur
                ]
            )
            ->add(
                'modele',
                TextType::class,
                [
                    'label' => 'Modèle',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->modele
                ]
            )
            ->add(
                'anneeDuModele',
                TextType::class,
                [
                    'label' => 'Année du modèle',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->anneeDuModele
                ]
            )
            ->add(
                'affectation',
                TextType::class,
                [
                    'label' => 'Affectation',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->affectation
                ]
            )
            ->add(
                'dateAchat',
                TextType::class,
                [
                    'label' => 'Date d’achat ',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $this->formattingService->formatDate($options["data"]->dateAchat)
                ]
            )


            // =============== ETAT MACHINE ===============
            ->add(
                'heureMachine',
                TextType::class,
                [
                    'label' => 'Heures machine',
                    'mapped' => false,
                    'disabled' => true,
                    'data' => $options["data"]->heureMachine
                ]
            )
            ->add(
                'kmMachine',
                TextType::class,
                [
                    'label' => 'Kilométrage',
                    'mapped' => false,
                    'disabled' => true,
                    'data' => $options["data"]->kmMachine
                ]
            )

            // =============== Agence, service et casier emetteur ============
            ->add('emetteur', AgenceServiceCasierType::class, [
                'render_type' => 'select',
                'disabled' => true,
                'label' => false,
                'required' => false,
                'agence_label' => 'Agence Emetteur',
                'service_label' => 'Service Emetteur',
                'agence_placeholder' => '-- Agence Emetteur --',
                'service_placeholder' => '-- Service Emetteur --',
            ])
            // =============== Agence, service et casier destinataire ============
            ->add('destinataire', AgenceServiceCasierType::class, [
                'render_type' => 'select',
                'label' => false,
                'required' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC && $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE,
                'agence_label' => 'Agence Destinataire',
                'service_label' => 'Service Destinataire',
                'casier_label' => 'Casier Destinataire',
                'agence_placeholder' => '-- Agence Destinataire --',
                'service_placeholder' => '-- Service Destinataire --',
                'casier_placeholder' => '-- Casier Destinataire --',
                'agence_class' => 'agenceDestinataire',
                'service_class' => 'serviceDestinataire',
                'casier_class' => 'casierDestinataire',
                'row_attr' => [
                    'id' => 'agence-service-destinataire',
                    'data-agences' => json_encode($agencesData),
                ],
                'agence_disabled' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                'service_disabled' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                'casier_disabled' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
            ])
            ->add(
                'motifMateriel',
                TextType::class,
                [
                    'label' => 'Motif',
                    'disabled' => $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC && $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE && $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER,
                    'required' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER,
                ]
            )
            // =============== Entrée en parc ===============
            ->add(
                'etatAchat',
                TextType::class,
                [
                    'label' => 'Etat à l\'achat',
                    'mapped' => false,
                    'disabled' => true,
                    'data' => $options["data"]->etatAchat,
                    'required' => false
                ]
            )
            ->add(
                'dateMiseLocation',
                DateType::class,
                [
                    'label' => 'Date de mise en location',
                    'widget' => 'single_text',
                    'html5' => true,
                    'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC,
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC,
                    'data' => $options["data"]->dateMiseLocation,
                ]
            )

            // =============== Valeur ===============
            ->add(
                'coutAcquisition',
                TextType::class,
                [
                    'label' => 'Coût d\'acquisition',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $this->formattingService->formatNumber($options["data"]->coutAcquisition)
                ]
            )
            ->add(
                'amortissement',
                TextType::class,
                [
                    'label' => 'Amortissement',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $this->formattingService->formatNumber($options["data"]->amortissement)
                ]
            )
            ->add(
                'valeurNetComptable',
                TextType::class,
                [
                    'label' => 'VNC',
                    'mapped' => false,
                    'disabled' => true,
                    'required' => false,
                    'data' => $this->formattingService->formatNumber($options["data"]->valeurNetComptable)
                ]
            )
            //================ CESSION ACTIF =================
            ->add(
                'nomClient',
                TextType::class,
                [
                    'label' => 'Nom client',
                    'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                ]
            )
            ->add(
                'modalitePaiement',
                ChoiceType::class,
                [
                    'label' => 'Modalité de paiement',
                    'choices' => self::MODE_PAYEMENT,
                    'placeholder' => ' -- Choisir mode paiement --',
                    'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                ]
            )
            ->add(
                'prixVenteHt',
                TextType::class,
                [
                    'label' => 'Prix HT',
                    'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                ]
            )
            // =============== MISE AU REBUT ==============
            ->add(
                'motifMiseRebut',
                TextType::class,
                [
                    'label' => 'Motif de mise au rebut',
                    'disabled' =>  $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                ]
            )
            ->add(
                'pieceJoint01',
                FileUploadType::class,
                [
                    'label' => 'Image (Merci de mettre un fichier image)',
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                    'disabled' =>  $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                    'allowed_mime_types' => ['image/jpeg', 'image/jpg', 'image/png'],
                    'accept' => '.jpeg, .jpg, .png',
                    'max_size' => '5M'
                ]
            )
            ->add(
                'pieceJoint02',
                FileUploadType::class,
                [
                    'label' => 'Fichier (Merci de mettre un fichier PDF)',
                    'required' => false,
                    'disabled' =>  $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                    'allowed_mime_types' => [
                        'application/pdf',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ],
                    'accept' => '.pdf, .docx, .xlsx',
                    'max_size' => '5M'
                ]
            )
            // =============== Mouvement matériel ===============
            ->add(
                'dateDemande',
                DateTimeType::class,
                [
                    'label' => 'Date',
                    'mapped' => false,
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'disabled' => true,
                    'required' => false,
                    'data' => $options["data"]->dateDemande
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecondFormDto::class,
            'validation_groups' => function (FormInterface $form) {
                /** @var SecondFormDto $data */
                $data = $form->getData();
                $groups = ['Default'];

                if ($data && $data->typeMouvement) {
                    $description = $data->typeMouvement->getDescription();

                    if (in_array($description, [
                        TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC,
                        TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE,
                        TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER,
                    ])) {
                        $groups[] = 'motif_materiel';
                    }

                    if ($description === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT) {
                        $groups[] = 'mise_au_rebut';
                    }
                }

                return $groups;
            },
        ]);
    }
}
