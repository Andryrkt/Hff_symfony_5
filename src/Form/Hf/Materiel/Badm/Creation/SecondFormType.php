<?php

namespace App\Form\Hf\Materiel\Badm\Creation;

use App\Form\Common\FileUploadType;
use Symfony\Component\Form\AbstractType;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Form\Common\AgenceServiceCasierType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SecondFormType extends AbstractType
{
    const MODE_PAYEMENT = [
        "TRAITE" => "TRAITE",
        "CHEQUE" => "CHEQUE",
        "VIREMENT" => "VIREMENT"
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typeMouvement = $options["data"]->typeMouvement->getDescription();

        $builder
            // =============== CARACTERISTIQUES DU MATERIEL ===============
            ->add(
                'designation',
                TextType::class,
                [
                    'label' => 'Désignation ',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->designation
                ]
            )
            ->add(
                'idMateriel',
                TextType::class,
                [
                    'label' => 'ID matériel',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->idMateriel
                ]
            )
            ->add(
                'numSerie',
                TextType::class,
                [
                    'label' => 'N° Série ',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->numSerie
                ]
            )
            ->add(
                'numParc',
                TextType::class,
                [
                    'label' => 'N° Parc',
                    'attr' => [
                        'disabled' => $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC
                    ],
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
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->groupe
                ]
            )
            ->add(
                'constructeur',
                TextType::class,
                [
                    'label' => 'Constructeur',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->constructeur
                ]
            )
            ->add(
                'modele',
                TextType::class,
                [
                    'label' => 'Modèle',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->modele
                ]
            )
            ->add(
                'anneeDuModele',
                TextType::class,
                [
                    'label' => 'Année du modèle',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->anneeDuModele
                ]
            )
            ->add(
                'affectation',
                TextType::class,
                [
                    'label' => 'Affectation',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->affectation
                ]
            )
            ->add(
                'dateAchat',
                TextType::class,
                [
                    'label' => 'Date d’achat ',
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->dateAchat
                ]
            )


            // =============== ETAT MACHINE ===============
            ->add(
                'heureMachine',
                TextType::class,
                [
                    'label' => 'Heures machine',
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->heureMachine
                ]
            )
            ->add(
                'kmMachine',
                TextType::class,
                [
                    'label' => 'Kilométrage',
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->kmMachine
                ]
            )

            //TODO: =============== Agence, service et casier emetteur ============
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
            //TODO: =============== Agence, service et casier destinataire ============
            ->add('destinataire', AgenceServiceCasierType::class, [
                'render_type' => 'select',
                'label' => false,
                'required' => false,
                'agence_label' => 'Agence Destinataire',
                'service_label' => 'Service Destinataire',
                'agence_placeholder' => '-- Agence Destinataire --',
                'service_placeholder' => '-- Service Destinataire --',
            ])
            ->add(
                'motifMateriel',
                TextType::class,
                [
                    'label' => 'Motif',
                    'attr' => [
                        'disabled' => $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC && $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE && $typeMouvement !== TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER,
                    ],
                    'required' => $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE || $typeMouvement === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_DE_CASIER,
                ]
            )
            // =============== Entrée en parc ===============
            ->add(
                'etatAchat',
                TextType::class,
                [
                    'label' => 'Etat à l\'achat',
                    'attr' => [
                        'disabled' => true
                    ],
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
                    //'format' => 'dd/MM/yyyy', 
                    'attr' => [
                        'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC
                    ],
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
                    'attr' => [
                        'disabled' => true
                    ],
                    'required' => false,
                    'data' => $options["data"]->coutAcquisition
                ]
            )
            ->add(
                'amortissement',
                TextType::class,
                [
                    'label' => 'Amortissement',
                    'attr' => [
                        'disabled' => true
                    ],
                    'required' => false,
                    'data' => $options["data"]->amortissement
                ]
            )
            ->add(
                'valeurNetComptable',
                TextType::class,
                [
                    'label' => 'VNC',
                    'attr' => [
                        'disabled' => true
                    ],
                    'required' => false,
                    'data' => $options["data"]->valeurNetComptable
                ]
            )
            //================ CESSION ACTIF =================
            ->add(
                'nomClient',
                TextType::class,
                [
                    'label' => 'Nom client',
                    'attr' => [
                        'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF
                    ],
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
                    'attr' => [
                        'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF
                    ],
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                ]
            )
            ->add(
                'prixVenteHt',
                TextType::class,
                [
                    'label' => 'Prix HT',
                    'attr' => [
                        'disabled' => $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF
                    ],
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF,
                ]
            )
            // =============== MISE AU REBUT ==============
            ->add(
                'motifMiseRebut',
                TextType::class,
                [
                    'label' => 'Motif de mise au rebut',
                    'attr' => [
                        'disabled' =>  $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT
                    ],
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                ]
            )
            ->add(
                'nomImage',
                FileUploadType::class,
                [
                    'label' => 'Image (Merci de mettre un fichier image)',
                    'required' => $options["data"]->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT,
                    'attr' => [
                        'disabled' =>  $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT
                    ],
                    'allowed_mime_types' => ['image/jpeg', 'image/jpg', 'image/png'],
                    'accept' => '.jpeg, .jpg, .png',
                    'max_size' => '5M'
                ]
            )
            ->add(
                'nomFichier',
                FileUploadType::class,
                [
                    'label' => 'Fichier (Merci de mettre un fichier PDF)',
                    'required' => false,
                    'attr' => [
                        'disabled' =>  $options["data"]->typeMouvement->getDescription() !== TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT
                    ],
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
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->dateDemande
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecondFormDto::class,
        ]);
    }
}
