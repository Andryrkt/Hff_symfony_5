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

    const SALARIE = [
        'PERMANENT' => 'PERMANENT',
        'TEMPORAIRE' => 'TEMPORAIRE',
    ];

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->security = $security;
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
                EntityType::class,
                [
                    'mapped' => false,
                    'label' => 'Matricule et nom',
                    'class' => Personnel::class,
                    'placeholder' => '-- choisir un personnel --',
                    'choice_label' => function (Personnel $personnel): string {
                        return $personnel->getMatricule() . ' ' . $personnel->getNom() . ' ' . $personnel->getPrenoms();
                    },
                    'multiple' => false, // Explicitly set to false
                    'required' => true,
                    'attr' => [
                        'data-controller' => 'tom-select',
                        'data-placeholder' => '-- choisir un personnel --'
                    ],
                    'choice_attr' => function (Personnel $personnel) {
                        return ['data-matricule' => $personnel->getMatricule()];
                    },
                    'query_builder' => function (PersonnelRepository $repo) use ($options) {
                        $qb = $repo->createQueryBuilder('p');
                        $user = $this->security->getUser();

                        if ($user instanceof User) {
                            $userAccesses = $user->getUserAccesses();

                            $agenceIds = [];
                            $serviceIds = [];
                            $allAgence = false;
                            $allService = false;

                            foreach ($userAccesses as $userAccess) {
                                if ($userAccess->getAllAgence()) {
                                    $allAgence = true;
                                    break;
                                }
                                if ($userAccess->getAgence()) {
                                    $agenceIds[] = $userAccess->getAgence()->getId();
                                }
                            }

                            foreach ($userAccesses as $userAccess) {
                                if ($userAccess->getAllService()) {
                                    $allService = true;
                                    break;
                                }
                                if ($userAccess->getService()) {
                                    $serviceIds[] = $userAccess->getService()->getId();
                                }
                            }

                            if (!$allAgence && !empty($agenceIds)) {
                                $qb->andWhere('p.agence IN (:agenceIds)')
                                    ->setParameter('agenceIds', $agenceIds);
                            }

                            if (!$allService && !empty($serviceIds)) {
                                $qb->andWhere('p.service IN (:serviceIds)')
                                    ->setParameter('serviceIds', $serviceIds);
                            }
                        }

                        return $qb->orderBy('p.matricule', 'ASC');
                    },
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
