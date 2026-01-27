<?php

namespace App\Form\Hf\Atelier\Dit;

use App\Constants\Hf\Dit\WorTypeDocumentConstants;
use Doctrine\ORM\EntityRepository;
use App\Dto\Hf\Atelier\Dit\FormDto;
use App\Entity\Hf\Atelier\Dit\CategorieAteApp;
use App\Form\Common\FileUploadType;
use App\Form\Common\AgenceServiceType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Hf\Atelier\Dit\WorTypeDocument;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\Hf\Atelier\Dit\WorTypeDocumentRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class DitFormType extends AbstractType
{
    const TYPE_REPARATION = [
        'EN COURS' => 'EN COURS',
        'DEJA EFFECTUEE' => 'DEJA EFFECTUEE',
        'A REALISER' => 'A REALISER'
    ];

    const REPARATION_REALISE = [
        'ATE TANA' => 'ATE TANA',
        'ATE POL TANA' => 'ATE POL TANA',
        'ATE STAR' => 'ATE STAR',
        'ATE MAS' => 'ATE MAS',
        'ATE TMV' => 'ATE TMV',
        'ATE FTU' => 'ATE FTU',
        'ATE ABV' => 'ATE ABV',
        'ATE LEV' => 'ATE LEV',
        'ENERGIE MAN' => 'ENERGIE MAN'
    ];

    const INTERNE_EXTERNE = [
        'INTERNE' => 'INTERNE',
        'EXTERNE' => 'EXTERNE'
    ];

    const OUI_NON = [
        'NON' => 'NON',
        'OUI' => 'OUI'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('estAtePolTana', CheckboxType::class, [
                'required' => false, // obligatoire false
                'label'    => "Intervention pneumatique",
            ])
            ->add('estDitAvoir', CheckboxType::class, [
                'required' => false, // obligatoire false
                'label'    => "Cette demande est un avoir (annulation de la" . $options['data']->numeroDit . ")",
            ])
            ->add('estDitRefacturation', CheckboxType::class, [
                'required' => false, //obligatoire false
                'label'    => "Cette demande est une refacturation (reprise de la DIT " . $options['data']->numeroDit . " avec nouvelle facturation>",
            ])
        ;

        $this->addInfoDit($builder);
        $this->addIntervention($builder);
        $this->addClient($builder);
        $this->addAgenceService($builder, $options['data']);
        $this->addReparation($builder);
        $this->addInfoMateriel($builder);
        $this->addPieceJoint($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FormDto::class,
        ]);
    }

    private function addInfoDit(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'objetDemande',
                TextType::class,
                [
                    'label' => 'Objet de la demande *',
                    'required' => true,
                    'attr' => ['class' => 'noEntrer']
                ]
            )
            ->add(
                'detailDemande',
                TextareaType::class,
                [
                    'label' => 'Détail de la demande *',
                    'required' => true,
                    'attr' => [
                        'rows' => 5,
                        'class' => 'detailDemande'
                    ]
                ]
            )
            ->add(
                'typeDocument',
                EntityType::class,
                [
                    'label' => 'Type de document *',
                    'placeholder' => '-- Choisir--',
                    'class' => WorTypeDocument::class,
                    'choice_label' => 'description',
                    'required' => true,
                    'query_builder' => function (WorTypeDocumentRepository $repository) {
                        return $repository->createQueryBuilder('w')
                            ->where('w.description IN (:description)')
                            ->setParameter('description', [WorTypeDocumentConstants::TYPE_DOCUMENT_MAINTENANCE_PREVENTIVE, WorTypeDocumentConstants::TYPE_DOCUMENT_MAINTENANCE_CURATIVE, WorTypeDocumentConstants::TYPE_DOCUMENT_AUTRES])
                            ->orderBy('w.description', 'ASC');
                    }
                ]
            )

            ->add(
                'categorieDemande',
                EntityType::class,
                [
                    'label' => 'Catégorie de demande *',
                    'placeholder' => '-- Choisir une catégorie --',
                    'class' => CategorieAteApp::class,
                    'choice_label' => 'libelleCategorieAteApp',
                    'required' => true
                ]
            )
            ->add(
                'interneExterne',
                ChoiceType::class,
                [
                    'label' => "Interne et Externe *",
                    'choices' => self::INTERNE_EXTERNE,
                    'placeholder' => false,
                    'data' => 'INTERNE',
                    'required' => false,
                    // 'attr' => [
                    //     'class' => 'interneExterne',
                    //     'data-informations' => json_encode([
                    //         'agenceId' => $options['data']->getAgence() ? $options['data']->getAgence()->getId() : null,
                    //         'serviceId' => $options['data']->getService() ? $options['data']->getService()->getId() : null
                    //     ])
                    // ]
                ]
            )



            ->add(
                'demandeDevis',
                ChoiceType::class,
                [
                    'label' => "Demande de devis *",
                    'choices' => self::OUI_NON,
                    'placeholder' => false,
                    'required' => false,
                    'data' => 'NON',
                    'attr' => [
                        'disabled' => true,
                    ]
                ]
            )

            ->add(
                'avisRecouvrement',
                ChoiceType::class,
                [
                    'label' => "Avis de recouvrement *",
                    'choices' => self::OUI_NON,
                    'placeholder' => false,
                    'required' => false,
                    'data' => 'NON'
                ]
            )


            ->add(
                'livraisonPartiel',
                ChoiceType::class,
                [
                    'label' => "Livraison Partielle *",
                    'choices' => self::OUI_NON,
                    'placeholder' => false,
                    'required' => false,
                    'data' => 'NON'
                ]
            )
        ;
    }
    private function addIntervention(FormBuilderInterface $builder)
    {
        $builder->add(
            'niveauUrgence',
            EntityType::class,
            [
                'label' => 'Niveau d\'urgence *',
                'label_html' => true,
                'placeholder' => false,
                'class' => WorNiveauUrgence::class,
                'choice_label' => 'code',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('n')
                        ->orderBy('n.code', 'DESC');
                },
            ]
        )
            ->add('datePrevueTravaux', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date prévue travaux *',
                'required' => true,
                'attr' => ['class' => 'noEntrer']
            ])
        ;
    }
    private function addReparation(FormBuilderInterface $builder)
    {
        $builder->add(
            'typeReparation',
            ChoiceType::class,
            [
                'label' => "Type de réparation *",
                'choices' => self::TYPE_REPARATION,
                'placeholder' => false,
                'required' => true,
                'data' => 'A REALISER'
            ]
        )
            ->add(
                'reparationRealise',
                ChoiceType::class,
                [
                    'label' => "Réparation réalisé par *",
                    'choices' => self::REPARATION_REALISE,
                    'placeholder' => '-- Choisir le répartion réalisé --',
                    'required' => true
                ]
            )
        ;
    }
    private function addClient(FormBuilderInterface $builder)
    {
        $builder->add(
            'nomClient',
            TextType::class,
            [
                'label' => 'Nom du client (*EXTERNE)',
                'required' => false,
                'attr' => [
                    'disabled' => true,
                    'class' => 'nomClient noEntrer autocomplete',
                    'autocomplete' => 'off',
                    'data-autocomplete-url' => 'autocomplete/all-client' //  la route de l'autocomplétion
                ]
            ]
        )
            ->add(
                'numeroClient',
                TextType::class,
                [
                    'label' => 'Numéro du client (*EXTERNE)',
                    'required' => false,
                    'attr' => [
                        'disabled' => true,
                        'class' => 'numClient noEntrer autocomplete',
                        'autocomplete' => 'off',
                        'data-autocomplete-url' => 'autocomplete/all-client' // la route de l'autocomplétion
                    ]
                ]
            )
            ->add(
                'numeroTel',
                TelType::class,
                [

                    'label' => 'N° téléphone (*EXTERNE)',
                    'required' => false,
                    'attr' => [
                        'disabled' => true,
                        'class' => 'numTel'
                    ]
                ]
            )
            ->add(
                'mailClient',
                EmailType::class,
                [

                    'label' => "E-mail du client (*EXTERNE)",
                    'required' => false,
                    'attr' => [
                        'class' => 'mailClient',
                        'disabled' => true,
                    ],
                ]
            )
            ->add('clientSousContrat', ChoiceType::class, [
                'label' => 'Client sous contrat',
                'choices' => self::OUI_NON,
                'placeholder' => false,
                'required' => false,
                'data' => 'NON',
                'attr' => [
                    'disabled' => true,
                    'class' => 'clientSousContrat'
                ]
            ])
        ;
    }

    private function addInfoMateriel(FormBuilderInterface $builder)
    {
        $builder->add(
            'idMateriel',
            TextType::class,
            [
                'label' => " Id Matériel *",
                'required' => true,
                'attr' => [
                    'class' => 'noEntrer autocomplete',
                    'autocomplete' => 'off',
                ]
            ]
        )
            ->add(
                'numParc',
                TextType::class,
                [
                    'label' => " N° Parc",
                    'required' => false,
                    'attr' => [
                        'class' => 'noEntrer autocomplete',
                        'autocomplete' => 'off',
                    ]
                ]

            )
            ->add(
                'numSerie',
                TextType::class,
                [
                    'label' => " N° Serie",
                    'required' => false,
                    'attr' => [
                        'class' => 'noEntrer autocomplete',
                        'autocomplete' => 'off',
                    ]
                ]
            )
        ;
    }
    private function addPieceJoint(FormBuilderInterface $builder)
    {
        $builder->add(
            'pieceJoint01',
            FileUploadType::class,
            [
                'label' => 'Pièce Jointe 01 (Merci de mettre un fichier PDF)',
                'required' => false,
                'allowed_mime_types' => ['application/pdf'],
                'accept' => '.pdf',
                'max_size' => '5M'
            ]
        )
            ->add(
                'pieceJoint02',
                FileUploadType::class,
                [
                    'label' => 'Pièce Jointe 02 (Merci de mettre un fichier PDF)',
                    'required' => false,
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                    'max_size' => '5M'
                ]
            )
            ->add(
                'pieceJoint03',
                FileUploadType::class,
                [
                    'label' => 'Pièce Jointe 03 (Merci de mettre un fichier PDF)',
                    'required' => false,
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                    'max_size' => '5M'
                ]
            )
        ;
    }

    private function addAgenceService(FormBuilderInterface $builder, $data): void
    {
        // $agences = $this->em->getRepository(Agence::class)->findAll();
        // $agencesData = [];
        // foreach ($agences as $agence) {
        //     $servicesData = [];
        //     foreach ($agence->getServices() as $service) {
        //         $servicesData[] = [
        //             'id' => $service->getId(),
        //             'code' => $service->getCode(),
        //             'nom' => $service->getNom(),
        //         ];
        //     }
        //     $agencesData[] = [
        //         'id' => $agence->getId(),
        //         'code' => $agence->getCode(),
        //         'nom' => $agence->getNom(),
        //         'services' => $servicesData,
        //         'casiers' => $casiersData,
        //     ];
        // }

        $builder
            ->add('agenceUser', TextType::class, [
                'mapped' => false,
                'label' => 'Agence',
                'required' => false,
                'attr' => ['disabled' => true],
                'data' => $data->agenceUser ?? null,
            ])
            ->add('serviceUser', TextType::class, [
                'mapped' => false,
                'label' => 'Service',
                'required' => false,
                'attr' => ['disabled' => true],
                'data' => $data->serviceUser ?? null,
            ])
            ->add('debiteur', AgenceServiceType::class, [
                'render_type' => 'select',
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
}
