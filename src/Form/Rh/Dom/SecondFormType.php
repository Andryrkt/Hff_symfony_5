<?php

namespace App\Form\Rh\Dom;

use App\Dto\Rh\Dom\SecondFormDto;
use App\Entity\Rh\Dom\Site;
use App\Form\Common\DateRangeType;
use App\Form\Common\FileUploadType;
use App\Form\Common\AgenceServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Repository\Rh\Dom\SiteRepository;
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
    const OUI_NON = [
        'NON' => 'NON',
        'OUI' => 'OUI'
    ];
    const DEVISE = [
        'MGA' => 'MGA',
        'EUR' => 'EUR',
        'USD' => 'USD'
    ];

    const MODE_PAYEMENT = [
        'MOBILE MONEY' => 'MOBILE MONEY',
        'ESPECES' => 'ESPECES',
        'VIREMENT BANCAIRE' => 'VIREMENT BANCAIRE',
    ];

    private $em;
    private $security;
    private $siteRepository;

    public function __construct(EntityManagerInterface $em, Security $security, SiteRepository $siteRepository)
    {
        $this->em = $em;
        $this->security = $security;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('debiteur', AgenceServiceType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'agence_label' => 'Agence Debiteur',
                'service_label' => 'Service Debiteur',
                'agence_placeholder' => '-- Agence Debiteur --',
                'service_placeholder' => '-- Service Debiteur --',
            ])
            ->add(
                'agenceUser',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'Agence',
                    'required' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->agenceUser ?? null
                ]
            )
            ->add(
                'serviceUser',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'Service',
                    'required' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->serviceUser ?? null
                ]
            )
            ->add(
                'dateDemande',
                DateTimeType::class,
                [
                    'label' => 'Date',
                    'mapped' => false,
                    'widget' => 'single_text',
                    'html5' => false,
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->dateDemande ?? null
                ]
            )
            ->add(
                'typeMission',
                TextType::class,
                [
                    'label' => 'Type de Mission :',
                    'attr' => [
                        'disabled' => true
                    ]
                ]
            )
            ->add(
                'categorie',
                TextType::class,
                [
                    'label' => 'Catégorie :',
                    'attr' => [
                        'disabled' => true
                    ]
                ]
            )
            ->add(
                'site',
                EntityType::class,
                [
                    'label' => 'Site:',
                    'class' => Site::class,
                    'choice_label' => 'nomZone',
                    'query_builder' => function () {
                        return $this->siteRepository->createQueryBuilder('s')
                            ->orderBy('s.nomZone', 'ASC');
                    },
                    // 'row_attr' => [
                    //     'style' => $idSousTypeDocument === 3 || $idSousTypeDocument === 4 || $idSousTypeDocument === 10 ? 'display: none;' : ''
                    // ],
                    // 'attr' => [
                    //     'disabled' => $idSousTypeDocument === 3 || $idSousTypeDocument === 4 || $idSousTypeDocument === 10,
                    // ]
                ]
            )
            ->add(
                'matricule',
                TextType::class,
                [
                    'label' => 'Matricule',
                    'attr' => [
                        'disabled' => true
                    ]
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Nom',
                    'attr' => [
                        'disabled' => true
                    ]
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'label' => 'Prénoms',
                    'attr' => [
                        'disabled' => true
                    ]
                ]
            )
            ->add(
                'cin',
                TextType::class,
                [
                    'label' => 'CIN',
                    'attr' => [
                        'disabled' => true,
                    ]
                ]
            )

            ->add('dateHeureMission', DateRangeType::class, [
                'label' => false,
                'debut_label' => 'Date début',
                'fin_label' => 'Date fin',
                'with_time' => true,
                'heure_debut_label' => 'Heure début',
                'heure_fin_label' => 'Heure fin'
            ])


            ->add(
                'nombreJour',
                TextType::class,
                [
                    'label' => 'Nombre de Jour',
                    'attr' => [
                        'readonly' => true
                    ]
                ]
            )
            ->add(
                'motifDeplacement',
                TextType::class,
                [
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
                ]
            )
            ->add('pieceJustificatif', CheckboxType::class, [
                'label' => 'Pièce à justifier',
                'required' => false,
            ])
            ->add(
                'client',
                TextType::class,
                [
                    'label' => 'Nom du client',
                    'required' => true,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le Client doit comporter au moins {{ limit }} caractères',
                            'max' => 50,
                            'maxMessage' => 'Le Client ne peut pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                ]
            )

            ->add(
                'fiche',
                TextType::class,
                [
                    'label' => 'N° fiche',
                    'required' => false,
                ]
            )

            ->add(
                'lieuIntervention',
                TextType::class,
                [
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
                ]
            )
            ->add(
                'vehiculeSociete',
                ChoiceType::class,
                [
                    'label' => "Véhicule société",
                    'choices' => self::OUI_NON,
                    'data' => "OUI",
                ]
            )
            ->add(
                'numVehicule',
                TextType::class,
                [
                    'label' => 'N°',
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le n° vehicule doit comporter au moins {{ limit }} caractères',
                            'max' => 10,
                            'maxMessage' => 'Le n° vehicule ne peut pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                ]
            )
            ->add(
                'idemnityDepl',
                TextType::class,
                [
                    'label' => 'Indemnité de chantier',
                    'required' => false
                ]
            )

            ->add(
                'totalIndemniteDeplacement',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'Total indemnité de chantier',
                    'attr' => [
                        'readonly' => true
                    ]
                ]
            )
            ->add(
                'devis',
                ChoiceType::class,
                [
                    'label' => 'Devise :',
                    'choices' => self::DEVISE,
                    'data' => 'MGA'
                ]
            )

            ->add(
                'supplementJournaliere',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'supplément journalier',
                    'required' => false,
                    'attr' => [
                        // 'disabled' => $idSousTypeDocument === 11,
                    ]
                ]
            )
            ->add(
                'indemniteForfaitaire',
                TextType::class,
                [
                    'label' => 'Indeminté forfaitaire journalière(s)',
                    // 'attr' => [
                    //     'readonly' => $idSousTypeDocument === 2 || $idSousTypeDocument === 5,
                    //     'disabled' => $idSousTypeDocument === 3 || $idSousTypeDocument === 4
                    // ],
                ]
            )
            ->add(
                'totalIndemniteForfaitaire',
                TextType::class,
                [
                    'label' => "Total de l'indemnite forfaitaire",
                    'attr' => [
                        'readonly' => true
                    ]
                ]
            )
            ->add(
                'motifAutresDepense1',
                TextType::class,
                [
                    'label' => 'Motif Autre dépense 1',
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le motif autre dépense 1 doit comporter au moins {{ limit }} caractères',
                            'max' => 30,
                            'maxMessage' => 'Le motif autre dépense 1 ne peut pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                ]
            )
            ->add(
                'autresDepense1',
                TextType::class,
                [
                    'label' => 'Montant',
                    'required' => false,
                ]
            )
            ->add(
                'motifAutresDepense2',
                TextType::class,
                [
                    'label' => 'Motif Autre dépense 2',
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le motif autre dépense 2 doit comporter au moins {{ limit }} caractères',
                            'max' => 30,
                            'maxMessage' => 'Le motif autre dépense 2 ne peut pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                ]
            )
            ->add(
                'autresDepense2',
                TextType::class,
                [
                    'label' => 'Montant',
                    'required' => false,
                ]
            )
            ->add(
                'motifAutresDepense3',
                TextType::class,
                [
                    'label' => 'Motif Autre dépense 3',
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le motif autre dépense 3 doit comporter au moins {{ limit }} caractères',
                            'max' => 30,
                            'maxMessage' => 'Le motif autre dépense 3 ne peut pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                ]
            )
            ->add(
                'autresDepense3',
                TextType::class,
                [
                    'label' => 'Montant',
                    'required' => false,
                ]
            )

            ->add(
                'totalAutresDepenses',
                TextType::class,
                [
                    'label' => 'Total Montant Autre Dépense',
                    'required' => true,
                    'attr' => [
                        'readonly' => true
                    ]
                ]
            )
            ->add(
                'totalGeneralPayer',
                TextType::class,
                [
                    'label' => 'Montant Total',
                    'required' => true,
                    'attr' => [
                        'readonly' => true
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le montant total ne peut pas être vide.',
                        ]),
                    ],
                ]
            )
            ->add(
                'modePayement',
                ChoiceType::class,
                [
                    'label' => 'Mode paiement',
                    'choices' => self::MODE_PAYEMENT
                ]
            )
            ->add(
                'mode',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'MOBILE MONEY',
                    'required' => true,
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Le Mode doit comporter au moins {{ limit }} caractères',
                            'max' => 30,
                            'maxMessage' => 'Le mode ne peut pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                    //'data' => $options['data']->getNumerotel()
                ]
            )
            ->add(
                'pieceJoint01',
                FileUploadType::class,
                [
                    'label' => 'Fichier Joint 01 (Merci de mettre un fichier PDF)',
                    //'required' => $salarier !== 'PERMANENT',
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                ]
            )
            ->add(
                'pieceJoint02',
                FileUploadType::class,
                [
                    'label' => 'Fichier Joint 02 (Merci de mettre un fichier PDF)',
                    //'required' => $salarier !== 'PERMANENT',
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SecondFormDto::class,
        ]);
    }
}
