<?php

namespace App\Form\Hf\Materiel\Casier\Creation;

use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\AgenceService\Agence;
use App\Dto\Hf\Materiel\Casier\SecondFormDto;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SecondFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'dateDemande',
                DateTimeType::class,
                [
                    'label' => 'Date',
                    'mapped' => false,
                    'widget' => 'single_text', // Utilisez le widget single_text pour une meilleure compatibilité
                    'html5' => false, // Désactivez l'HTML5 si vous souhaitez un format spécifique
                    'format' => 'dd/MM/yyyy',
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->dateDemande
                ]
            )
            ->add(
                'agenceRattacher',
                EntityType::class,
                [
                    'label' => 'Agence rattacher',
                    'placeholder' => '-- Choisir une agence  --',
                    'class' => Agence::class,
                    'choice_label' => function (Agence $agence): string {
                        return $agence->getCode() . ' ' . $agence->getNom();
                    },
                    'required' => false,
                    'query_builder' => function (AgenceRepository $agenceRepository) {
                        return $agenceRepository->createQueryBuilder('a')->orderBy('a.code', 'ASC');
                    },
                    'required' => true
                ]
            )
            ->add(
                'motif',
                TextType::class,
                [
                    'label' => 'Motif de création',
                    'required' => true
                ]
            )
            ->add(
                'client',
                TextType::class,
                [
                    'label' => 'Client',
                    'required' => true,
                ]
            )
            ->add(
                'chantier',
                TextType::class,
                [
                    'label' => 'Chantier',
                    'required' => true,
                ]
            )
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
                    'mapped' => false,
                    'attr' => [
                        'disabled' => true
                    ],
                    'data' => $options["data"]->numParc
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SecondFormDto::class,
        ]);
    }
}
