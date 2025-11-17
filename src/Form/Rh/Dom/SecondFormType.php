<?php

namespace App\Form\Rh\Dom;

use App\Dto\Rh\Dom\SecondFormDto;
use App\Entity\Rh\Dom\Site;
use App\Entity\Rh\Dom\SousTypeDocument;
use App\Form\Common\DateRangeType;
use App\Form\Common\FileUploadType;
use App\Form\Common\AgenceServiceType;
use App\Repository\Rh\Dom\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SecondFormType extends AbstractType
{
    private const OUI_NON = [
        'NON' => 'NON',
        'OUI' => 'OUI'
    ];

    private const DEVISE = [
        'MGA' => 'MGA',
        'EUR' => 'EUR',
        'USD' => 'USD'
    ];

    private const MODE_PAYEMENT = [
        'MOBILE MONEY' => 'MOBILE MONEY',
        'ESPECES' => 'ESPECES',
        'VIREMENT BANCAIRE' => 'VIREMENT BANCAIRE',
    ];

    private EntityManagerInterface $em;
    private Security $security;
    private SiteRepository $siteRepository;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        SiteRepository $siteRepository
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];
        $typeMission = $data->typeMission;
        $isSpecialMission = $this->isSpecialMissionType($typeMission);

        $this->addDebiteurSection($builder, $data);
        $this->addUserInfoSection($builder, $data);
        $this->addMissionSection($builder, $data, $typeMission, $isSpecialMission);
        $this->addEmployeeSection($builder, $options);
        $this->addDateSection($builder);
        $this->addMotifSection($builder);
        $this->addVehicleSection($builder);
        $this->addIndemnitiesSection($builder, $data, $typeMission, $isSpecialMission);
        $this->addOtherExpensesSection($builder);
        $this->addPaymentSection($builder);
        $this->addAttachmentsSection($builder, $data);
    }

    private function isSpecialMissionType(?object $typeMission): bool
    {
        if (!$typeMission) {
            return false;
        }

        $code = $typeMission->getCodeSousType();
        return in_array($code, [
            SousTypeDocument::CODE_COMPLEMENT,
            SousTypeDocument::CODE_MUTATION,
            SousTypeDocument::CODE_FRAIS_EXCEPTIONNEL
        ], true);
    }

    private function addDebiteurSection(FormBuilderInterface $builder, $data): void
    {
        $builder->add('debiteur', AgenceServiceType::class, [
            'label' => false,
            'required' => false,
            'mapped' => false,
            'agence_label' => 'Agence Débiteur',
            'service_label' => 'Service Débiteur',
            'agence_placeholder' => '-- Agence Débiteur --',
            'service_placeholder' => '-- Service Débiteur --',
            'data' => $data->debiteur ?? []
        ]);
    }

    private function addUserInfoSection(FormBuilderInterface $builder, $data): void
    {
        $builder
            ->add('agenceUser', TextType::class, [
                'mapped' => false,
                'label' => 'Agence',
                'required' => false,
                'attr' => ['disabled' => true],
                'data' => $data->agenceUser ?? null,
                'mapped' => false,
            ])
            ->add('serviceUser', TextType::class, [
                'mapped' => false,
                'label' => 'Service',
                'required' => false,
                'attr' => ['disabled' => true],
                'data' => $data->serviceUser ?? null,
                'mapped' => false,
            ])
            ->add('dateDemande', DateTimeType::class, [
                'label' => 'Date',
                'mapped' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => ['disabled' => true],
                'data' => $data->dateDemande ?? null,
                'mapped' => false,
            ]);
    }

    private function addMissionSection(
        FormBuilderInterface $builder,
        $data,
        $typeMission,
        bool $isSpecialMission
    ): void {
        $builder
            ->add('typeMission', TextType::class, [
                'label' => 'Type de Mission :',
                'attr' => ['disabled' => true],
                'mapped' => false,
                'data' => $typeMission->getCodeSousType()
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie :',
                'row_attr' => [
                    'style' => $isSpecialMission ? 'display: none;' : ''
                ],
                'attr' => ['disabled' => true],
                'mapped' => false,
                'data' => $data->categorie->getDescription()
            ])
            ->add('site', EntityType::class, [
                'label' => 'Site :',
                'class' => Site::class,
                'choice_label' => 'nomZone',
                'query_builder' => fn() => $this->getSiteQueryBuilder($data),
                'row_attr' => [
                    'style' => $isSpecialMission ? 'display: none;' : ''
                ],
                'attr' => ['disabled' => $isSpecialMission],
                'data' => $data->site
            ]);
    }

    private function getSiteQueryBuilder($data)
    {
        return $this->siteRepository->createQueryBuilder('s')
            ->join('s.indemnites', 'i')
            ->where('i.categorieId = :categorie')
            ->andWhere('i.rmqId = :rmq')
            ->andWhere('i.sousTypeDocumentId = :typeMission')
            ->setParameters([
                'categorie' => $data->categorie,
                'rmq' => $data->rmq,
                'typeMission' => $data->typeMission
            ])
            ->orderBy('s.nomZone', 'ASC');
    }

    private function addEmployeeSection(FormBuilderInterface $builder, $options): void
    {
        $disabledAttr = ['disabled' => true];

        $builder
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'attr' => $disabledAttr,
                'mapped' => false,
                'data' => $options['data']->matricule
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => $disabledAttr,
                'mapped' => false,
                'data' => $options['data']->nom
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénoms',
                'attr' => $disabledAttr,
                'mapped' => false,
                'data' => $options['data']->prenom
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => $disabledAttr,
                'mapped' => false,
                'data' => $options['data']->cin
            ]);
    }

    private function addDateSection(FormBuilderInterface $builder): void
    {
        $builder
            ->add('dateHeureMission', DateRangeType::class, [
                'label' => false,
                'debut_label' => 'Date début',
                'fin_label' => 'Date fin',
                'with_time' => true,
                'heure_debut_label' => 'Heure début',
                'heure_fin_label' => 'Heure fin'
            ])
            ->add('nombreJour', TextType::class, [
                'label' => 'Nombre de jours',
                'attr' => ['readonly' => true]
            ]);
    }

    private function addMotifSection(FormBuilderInterface $builder): void
    {
        $builder
            ->add('motifDeplacement', TextType::class, [
                'label' => 'Motif',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le motif de déplacement ne peut pas être vide.']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le motif de déplacement doit comporter au moins {{ limit }} caractères',
                        'max' => 100,
                        'maxMessage' => 'Le motif de déplacement ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('pieceJustificatif', CheckboxType::class, [
                'label' => 'Pièce à justifier',
                'required' => false,
            ])
            ->add('client', TextType::class, [
                'label' => 'Nom du client',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le client doit comporter au moins {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Le client ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('fiche', TextType::class, [
                'label' => 'N° fiche',
                'required' => false,
            ])
            ->add('lieuIntervention', TextType::class, [
                'label' => 'Lieu d\'intervention',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Le lieu d\'intervention ne peut pas être vide.']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le lieu doit comporter au moins {{ limit }} caractères',
                        'max' => 100,
                        'maxMessage' => 'Le lieu ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ]);
    }

    private function addVehicleSection(FormBuilderInterface $builder): void
    {
        $builder
            ->add('vehiculeSociete', ChoiceType::class, [
                'label' => 'Véhicule société',
                'choices' => self::OUI_NON,
                'data' => 'OUI',
            ])
            ->add('numVehicule', TextType::class, [
                'label' => 'N°',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le n° véhicule doit comporter au moins {{ limit }} caractères',
                        'max' => 10,
                        'maxMessage' => 'Le n° véhicule ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ]);
    }

    private function addIndemnitiesSection(
        FormBuilderInterface $builder,
        $data,
        $typeMission,
        bool $isSpecialMission
    ): void {
        $builder
            ->add('idemnityDepl', TextType::class, [
                'label' => 'Indemnité de chantier',
                'required' => false,
            ])
            ->add('totalIndemniteDeplacement', TextType::class, [
                'label' => 'Total indemnité de chantier',
                'attr' => ['readonly' => true],
            ])
            ->add('devis', ChoiceType::class, [
                'label' => 'Devise :',
                'choices' => self::DEVISE,
                'data' => 'MGA'
            ])
            ->add('supplementJournaliere', TextType::class, [
                'label' => 'Supplément journalier',
                'required' => false,
                'attr' => [
                    'disabled' => $typeMission === SousTypeDocument::CODE_TROP_PERCU,
                ],
            ])
            ->add('indemniteForfaitaire', TextType::class, [
                'label' => 'Indemnité forfaitaire journalière(s)',
                'attr' => [
                    'readonly' => in_array($typeMission->getCodeSousType(), [
                        SousTypeDocument::CODE_MISSION,
                        SousTypeDocument::CODE_MUTATION
                    ], true),
                    'disabled' => $isSpecialMission
                ],
                'data' => $data->indemniteForfaitaire,
            ])
            ->add('totalIndemniteForfaitaire', TextType::class, [
                'label' => "Total de l'indemnité forfaitaire",
                'attr' => ['readonly' => true],
            ]);
    }

    private function addOtherExpensesSection(FormBuilderInterface $builder): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $builder
                ->add("motifAutresDepense{$i}", TextType::class, [
                    'label' => "Motif Autre dépense {$i}",
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => "Le motif autre dépense {$i} doit comporter au moins {{ limit }} caractères",
                            'max' => 30,
                            'maxMessage' => "Le motif autre dépense {$i} ne peut pas dépasser {{ limit }} caractères",
                        ]),
                    ],
                ])
                ->add("autresDepense{$i}", TextType::class, [
                    'label' => 'Montant',
                    'required' => false,

                ]);
        }

        $builder
            ->add('totalAutresDepenses', TextType::class, [
                'label' => 'Total Montant Autres Dépenses',
                'required' => true,
                'attr' => ['readonly' => true],
            ])
            ->add('totalGeneralPayer', TextType::class, [
                'label' => 'Montant Total',
                'required' => true,
                'attr' => ['readonly' => true],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le montant total ne peut pas être vide.',
                    ]),
                ],
            ]);
    }

    private function addPaymentSection(FormBuilderInterface $builder): void
    {
        $builder
            ->add('modePayement', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => self::MODE_PAYEMENT
            ])
            ->add('mode', TextType::class, [
                'label' => 'MOBILE MONEY',
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le mode doit comporter au moins {{ limit }} caractères',
                        'max' => 30,
                        'maxMessage' => 'Le mode ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ]);
    }

    private function addAttachmentsSection(FormBuilderInterface $builder, $data): void
    {
        $isRequired = $data->salarier !== 'PERMANENT';

        for ($i = 1; $i <= 2; $i++) {
            $builder->add("pieceJoint0{$i}", FileUploadType::class, [
                'label' => "Fichier Joint 0{$i} (Merci de mettre un fichier PDF)",
                'required' => $isRequired,
                'allowed_mime_types' => ['application/pdf'],
                'accept' => '.pdf',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecondFormDto::class,
        ]);
    }
}
