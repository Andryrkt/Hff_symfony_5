<?php

namespace App\Form\Rh\Dom;

use App\Dto\Rh\Dom\FirstFormDto;
use App\Entity\Rh\Dom\Categorie;
use App\Entity\Rh\Dom\SousTypeDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class FirstFormType extends AbstractType
{
    private $em;

    const SALARIE = [
        'PERMANENT' => 'PERMANENT',
        'TEMPORAIRE' => 'TEMPORAIRE',
    ];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add(
                'agenceUser',
                TextType::class,
                [
                    'mapped' => false,
                    'label' => 'Agence',
                    'required' => false,
                    'attr' => [
                        'readonly' => true
                    ],
                    // 'data' => $options["data"]->getAgenceEmetteur() ?? null
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
                        'readonly' => true,
                    ],
                    // 'data' => $options["data"]->getServiceEmetteur() ?? null
                ]
            )
            ->add(
                'typeMission',
                EntityType::class,
                [
                    'label' => 'Type de Mission',
                    'class' => SousTypeDocument::class,
                    'choice_label' => 'codeSousType',
                    // 'query_builder' => function (SousTypeDocumentRepository $repo) {
                    //     return $repo->createQueryBuilder('s')
                    //         ->where('s.id NOT IN (:excludedIds)')
                    //         ->setParameter('excludedIds', [5, 11]); // id de mutation et trop perçu
                    // }
                ]
            )
            ->add(
                'salarier',
                ChoiceType::class,
                [
                    'mapped' => false,
                    'label' => 'Salarié',
                    'choices' => self::SALARIE,
                    'data' => 'PERMANENT'
                ]
            )
            ->add(
                'matricule',
                TextType::class,
                [
                    'label' => 'Matricule',
                    'attr' => [
                        'readonly' => true
                    ],
                    'required' => true
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Nom',
                    'required' => true
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'label' => 'Prénoms',
                    'required' => true
                ]
            )
            ->add(
                'cin',
                TextType::class,
                [
                    'label' => 'CIN',
                    'required' => true,
                ]
            )
            ->add(
                'categorie',
                EntityType::class,
                [
                    'label' => 'Catégorie',
                    'class' => Categorie::class,
                    'choice_label' => 'description',
                    'choices' => [],
                    'placeholder' => false,
                    'required' => false,
                    'empty_data' => null,
                    'mapped' => true,
                    'invalid_message' => 'Veuillez sélectionner une catégorie valide.',
                ]
            )
        
        ;
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FirstFormDto::class,
        ]);
    }
}
