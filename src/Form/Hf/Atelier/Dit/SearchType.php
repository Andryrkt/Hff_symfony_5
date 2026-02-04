<?php

namespace App\Form\Hf\Atelier\Dit;

use App\Form\Common\DateRangeType;
use Doctrine\ORM\EntityRepository;
use App\Dto\Hf\Atelier\Dit\SearchDto;
use App\Form\Common\AgenceServiceType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Atelier\Dit\CategorieAteApp;
use App\Entity\Hf\Atelier\Dit\WorTypeDocument;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SearchType extends AbstractType
{
    const INTERNE_EXTERNE = [
        'INTERNE' => 'INTERNE',
        'EXTERNE' => 'EXTERNE'
    ];

    const ETAT_FACTURE = [
        'Complètement facturé' => 'Complètement facturé',
        'Partiellement facturé' => 'Partiellement facturé',
        'A valider client interne' => 'A valider client interne'
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

    private $ditRepository;

    public function __construct(DitRepository $ditRepository)
    {
        $this->ditRepository = $ditRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'niveauUrgence',
                EntityType::class,
                [
                    'label' => 'Niveau d\'urgence',
                    'label_html' => true,
                    'placeholder' => '-- Choisir un niveau d\'urgence --',
                    'class' => WorNiveauUrgence::class,
                    'choice_label' => 'code',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('n')
                            ->orderBy('n.code', 'DESC');
                    },
                ]
            )
            ->add('statut', EntityType::class, [
                'label'         => 'Statut',
                'class'         => StatutDemande::class,
                'choice_label'  => 'description',
                'placeholder'   => '-- Choisir un statut --',
                'required'      => false,
                'query_builder' => function (StatutDemandeRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.codeApplication = :codeApp')
                        ->setParameter('codeApp', 'DOM');
                },
            ])
            ->add('idMateriel', NumberType::class, [
                'label' => 'Id Matériel',
                'required' => false,
            ])
            ->add('numParc', TextType::class, [
                'label' => "N° Parc",
                'required' => false
            ])
            ->add('numSerie', TextType::class, [
                'label' => "N° Série",
                'required' => false
            ])
            ->add('typeDocument', EntityType::class, [
                'label' => 'Type de Document',
                'class' => WorTypeDocument::class,
                'choice_label' => 'description',
                'placeholder' => '-- Choisir un type de document--',
                'required' => false,
            ])
            ->add(
                'internetExterne',
                ChoiceType::class,
                [
                    'label' => "Interne - Externe",
                    'choices' => self::INTERNE_EXTERNE,
                    'placeholder' => '-- Choisir --',
                    'required' => false,
                    'data' => $options['interne_externe'] ?? '',
                    'attr' => ['class' => 'interneExterne']
                ]
            )
            ->add('dateDemande', DateRangeType::class, [
                'label' => false,
                'debut_label' => 'Date début demande',
                'fin_label' => 'Date fin demande',
                'with_time' => false,
            ])


            ->add(
                'numDit',
                TextType::class,
                [
                    'label' => 'N° DIT',
                    'required' => false
                ]
            )
            ->add(
                'numOr',
                NumberType::class,
                [
                    'label' => 'N° OR',
                    'required' => false
                ]
            )
            ->add(
                'statutOr',
                ChoiceType::class,
                [
                    'label' => 'Statut OR',
                    'required' => false,
                    'choices' => $this->statutOr(),
                    'placeholder' => '-- choisir une statut --'
                ]
            )
            ->add(
                'ditSansOr',
                CheckboxType::class,
                [
                    'label' => 'DIT sans OR',
                    'required' => false
                ]
            )

            ->add(
                'categorie',
                EntityType::class,
                [
                    'label' => 'Catégorie de demande',
                    'placeholder' => '-- Choisir une catégorie --',
                    'class' => CategorieAteApp::class,
                    'choice_label' => 'libelleCategorieAteApp',
                    'required' => false,
                ]
            )
            ->add(
                'utilisateur',
                TextType::class,
                [
                    'label' => 'Utilisateur',
                    'required' => false
                ]
            )
            ->add(
                'sectionAffectee',
                ChoiceType::class,
                [
                    'label' => 'Section affectée',
                    'required' => false,
                    'choices' => $this->sectionAffectee(),
                    'placeholder' => '-- choisir une section --'
                ]
            )
            ->add(
                'sectionSupport1',
                ChoiceType::class,
                [
                    'label' => 'Section support 1',
                    'placeholder' => '-- choisir une section --',
                    'required' => false,
                    'choices' => $this->sectionSupport1(),

                ]
            )
            ->add(
                'sectionSupport2',
                ChoiceType::class,
                [
                    'label' => 'Section support 2',
                    'placeholder' => '-- choisir une section --',
                    'required' => false,
                    'choices' => $this->sectionSupport2(),

                ]
            )
            ->add(
                'sectionSupport3',
                ChoiceType::class,
                [
                    'label' => 'Section support 3',
                    'placeholder' => '-- choisir une section --',
                    'required' => false,
                    'choices' => $this->sectionSupport3(),

                ]
            )
            ->add(
                'etatFacture',
                ChoiceType::class,
                [
                    'label' => "Statut facture",
                    'choices' => self::ETAT_FACTURE,
                    'placeholder' => '-- Choisir --',
                    'required' => false,
                ]
            )
            ->add(
                'numDevis',
                TextType::class,
                [
                    'label' => 'N° devis',
                    'required' => false
                ]
            )
            ->add(
                'reparationRealise',
                ChoiceType::class,
                [
                    'label' => "Réalisé par",
                    'choices' => self::REPARATION_REALISE,
                    'placeholder' => '-- Choisir le répartion réalisé --',
                    'required' => false,
                ]
            )
            ->add('emetteur', AgenceServiceType::class, [
                'render_type' => 'select',
                'label' => false,
                'required' => false,
                'mapped' => true,
                'agence_label' => 'Agence Emetteur',
                'service_label' => 'Service Emetteur',
                'agence_placeholder' => '-- Agence Emetteur --',
                'service_placeholder' => '-- Service Emetteur --',
                'agence_class' => 'agenceEmetteur', // Class used by JS/Macro
                'service_class' => 'serviceEmetteur', // Class used by JS/Macro
            ])
            ->add('debiteur', AgenceServiceType::class, [
                'render_type' => 'select',
                'label' => false,
                'required' => false,
                'mapped' => true,
                'agence_label' => 'Agence Débiteur',
                'service_label' => 'Service Débiteur',
                'agence_placeholder' => '-- Agence Débiteur --',
                'service_placeholder' => '-- Service Débiteur --',
                'agence_class' => 'agenceDebiteur',
                'service_class' => 'serviceDebiteur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchDto::class,
        ]);
    }

    private function statutOr()
    {
        $statutOr = $this->ditRepository->findStatutOr();

        return array_combine($statutOr, $statutOr);
    }

    private function sectionAffectee()
    {
        $sectionAffecte = $this->ditRepository->findSectionAffectee();
        $groupes = ['Chef section', 'Chef de section', 'Responsable section', 'Chef d\'équipe']; // Les groupes de mots à supprimer
        $sectionAffectee = str_replace($groupes, "", $sectionAffecte);
        return array_combine($sectionAffectee, $sectionAffectee);
    }

    private function sectionSupport1()
    {
        $sectionSupport1 = $this->ditRepository->findSectionSupport1();
        $groupes = ['Chef section', 'Chef de section', 'Responsable section', 'Chef d\'équipe']; // Les groupes de mots à supprimer
        $sectionSupport1 = str_replace($groupes, "", $sectionSupport1);
        return array_combine($sectionSupport1, $sectionSupport1);
    }

    private function sectionSupport2()
    {
        $sectionSupport2 = $this->ditRepository->findSectionSupport2();
        $groupes = ['Chef section', 'Chef de section', 'Responsable section', 'Chef d\'équipe']; // Les groupes de mots à supprimer
        $sectionSupport2 = str_replace($groupes, "", $sectionSupport2);
        return array_combine($sectionSupport2, $sectionSupport2);
    }

    private function sectionSupport3()
    {
        $sectionSupport3 = $this->ditRepository->findSectionSupport3();
        $groupes = ['Chef section', 'Chef de section', 'Responsable section', 'Chef d\'équipe']; // Les groupes de mots à supprimer
        $sectionSupport3 = str_replace($groupes, "", $sectionSupport3);
        return array_combine($sectionSupport3, $sectionSupport3);
    }
}
