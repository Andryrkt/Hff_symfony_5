<?php

namespace App\Form\Hf\Rh\Dom;

use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Repository\Hf\Rh\Dom\CategorieRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\Hf\Rh\Dom\SousTypeDocumentRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Security;
use App\Entity\Admin\PersonnelUser\User;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;


class FirstFormType extends AbstractType
{
    private $security;
    private $personnelRepository;

    const SALARIE = [
        'PERMANENT' => 'PERMANENT',
        'TEMPORAIRE' => 'TEMPORAIRE',
    ];

    public function __construct(EntityManagerInterface $em, Security $security, PersonnelRepository $personnelRepository)
    {
        $this->security = $security;
        $this->personnelRepository = $personnelRepository;
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
                        'readonly' => true,
                    ],
                    'data' => $options["data"]->serviceUser ?? null
                ]
            )
            ->add(
                'typeMission',
                EntityType::class,
                [
                    'label' => 'Type de Mission',
                    'class' => SousTypeDocument::class,
                    'choice_label' => 'codeSousType',
                    'query_builder' => function (SousTypeDocumentRepository $repo) {
                        return $repo->createQueryBuilder('s')
                            ->where('s.codeSousType NOT IN (:excludedIds)')
                            ->setParameter('excludedIds', ['MUTATION', 'TROP PERCU']); // id de mutation et trop perçu
                    }
                ]
            )
            ->add(
                'salarier',
                ChoiceType::class,
                [
                    'label' => 'Salarié',
                    'choices' => self::SALARIE,
                    'data' => 'PERMANENT'
                ]
            )
            ->add(
                'matricule',
                TextType::class,
                [
                    'label' => 'Matricule *',
                    'attr' => [
                        'readonly' => true
                    ]
                ]
            )
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Nom *',
                    'required' => true
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'label' => 'Prénoms *',
                    'required' => true
                ]
            )
            ->add(
                'cin',
                TextType::class,
                [
                    'label' => 'CIN *',
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
                    'placeholder' => false,
                    'required' => false,
                    'empty_data' => null,
                    'mapped' => true,
                    'invalid_message' => 'Veuillez sélectionner une catégorie valide.',
                    'query_builder' => function (CategorieRepository $repository) use ($options) {
                        $descriptionRmq = explode('-', $options['data']->agenceUser)[0] == '50' ? '50' : 'STD';
                        return $repository->createQueryBuilder('c')
                            ->leftJoin('c.rmq', 'r')
                            ->where('r.description = :description')
                            ->setParameter('description', $descriptionRmq)
                            ->orderBy('c.description', 'ASC');
                    },
                ]
            )
            ->add(
                'matriculeNom',
                ChoiceType::class,
                [
                    'mapped' => false,
                    'label' => 'Matricule et nom *',
                    'choices' => $this->personnelRepository->findChoicesForUser($this->security->getUser()),
                    'placeholder' => '-- choisir un personnel --',
                    'multiple' => false,
                    'required' => false, // Validation gérée manuellement par first_form_controller.ts
                    'attr' => [
                        'data-controller' => 'tom-select',
                        'data-placeholder' => '-- choisir un personnel --'
                    ],
                    'choice_attr' => function ($val, $key, $index) {
                        // $key est le Label (ex: "8450 NOM Prenom")
                        // On extrait le matricule pour le data-attribute
                        $matricule = explode(' ', $key)[0] ?? '';
                        return ['data-matricule' => $matricule];
                    },
                ]
            );
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FirstFormDto::class,
        ]);
    }
}
