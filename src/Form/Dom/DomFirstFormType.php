<?php

namespace App\Form\Dom;

use App\DataFixtures\dom\SousTypeDocumentFixtures;
use App\Dto\Dom\DomFirstFormData;
use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomIndemnite;
use App\Entity\Dom\DomRmq;
use App\Entity\Dom\DomSousTypeDocument;
use App\Form\Shared\AgenceServiceEmetteurType;
use App\Repository\Admin\PersonnelUser\UserAccessRepository;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class DomFirstFormType extends AbstractType
{
    private const SALARIE = [
        'PERMANENT' => 'PERMANENT',
        'TEMPORAIRE' => 'TEMPORAIRE',
    ];

    private const EXCLUDED_CODE_SOUS_TYPE_DOC = [
        DomSousTypeDocument::CODE_SOUS_TYPE_MUTATION,
        DomSousTypeDocument::CODE_SOUS_TYPE_TROP_PERCU
    ];

    private $entityManager;
    private $security;
    private $userAccessRepository;
    private $sousTypeDocumentRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security,
        UserAccessRepository $userAccessRepository,
        DomSousTypeDocumentRepository $sousTypeDocumentRepository
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->userAccessRepository = $userAccessRepository;
        $this->sousTypeDocumentRepository = $sousTypeDocumentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emetteur', AgenceServiceEmetteurType::class, [
                'required' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('sousTypeDocument', EntityType::class, [
                'label' => 'Type de Mission',
                'class' => DomSousTypeDocument::class,
                'choice_label' => 'codeSousType',
                'query_builder' => $this->findAllowedTypesQueryBuilder(),
                'placeholder' => 'Sélectionnez un type de mission',
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => DomCategorie::class,
                'choice_label' => 'description',
                'choices' => [],
                'placeholder' => 'Sélectionnez d\'abord un type de mission',
            ])
            ->add('salarie', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Salarié',
                'choices' => self::SALARIE,
                'data' => 'PERMANENT',
                'expanded' => false,
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'attr' => ['readonly' => true],
                'required' => true
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénoms',
                'required' => true
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'required' => true,
            ]);

        $this->addMatriculeNomField($builder, $this->security->getUser());
        $this->setupEventListeners($builder);
    }

    private function addMatriculeNomField($form, $user): void
    {
        $form->add('matriculeNom', EntityType::class, [
            'mapped' => false,
            'label' => 'Matricule et nom',
            'class' => Personnel::class,
            'placeholder' => '-- choisir un personnel --',
            'choice_label' => fn(Personnel $personnel) => sprintf(
                '%s %s %s',
                $personnel->getMatricule(),
                $personnel->getNom(),
                $personnel->getPrenoms()
            ),
            'required' => true,
            'query_builder' => function (PersonnelRepository $personnelRepository) use ($user) {
                // Optimisation : charger les accès en une seule requête avec jointures
                $accesses = $this->userAccessRepository->createQueryBuilder('ua')
                    ->leftJoin('ua.agence', 'a')
                    ->leftJoin('ua.service', 's')
                    ->where('ua.users = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult();

                $qb = $personnelRepository->createQueryBuilder('p');

                foreach ($accesses as $access) {
                    if ($access->getAccessType() === 'ALL') {
                        return $qb->orderBy('p.matricule', 'ASC');
                    }
                }

                $orX = $qb->expr()->orX();
                foreach ($accesses as $i => $access) {
                    $andX = $qb->expr()->andX();
                    if ($access->getAgence()) {
                        $andX->add($qb->expr()->eq('p.agence', ":agence{$i}"));
                        $qb->setParameter("agence{$i}", $access->getAgence());
                    }
                    if ($access->getService()) {
                        $andX->add($qb->expr()->eq('p.service', ":service{$i}"));
                        $qb->setParameter("service{$i}", $access->getService());
                    }
                    $orX->add($andX);
                }

                if ($orX->count() > 0) {
                    $qb->where($orX);
                } else {
                    $qb->where('1 = 0');
                }

                return $qb->orderBy('p.matricule', 'ASC');
            },
        ]);
    }

    private function setupEventListeners(FormBuilderInterface $builder): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (isset($data['sousTypeDocument']) && isset($data['emetteur']['agenceEmetteur'])) {
                $sousTypeDocument = $this->entityManager
                    ->getRepository(DomSousTypeDocument::class)
                    ->find($data['sousTypeDocument']);

                $this->addCategorieField($form, $sousTypeDocument, $data['emetteur']['agenceEmetteur']);
            }
        });
    }

    private function addCategorieField($form, ?DomSousTypeDocument $sousTypeDocument, ?string $agenceEmetteur): void
    {
        if (!$sousTypeDocument || !$agenceEmetteur) {
            return;
        }

        $rmqDescription = str_starts_with($agenceEmetteur, '50') ? DomRmq::DESCRIPTION_50 : DomRmq::DESCRIPTION_STD;

        $categories = $this->entityManager->createQueryBuilder()
            ->select('DISTINCT c')
            ->from(DomCategorie::class, 'c')
            ->join('c.domIndemnites', 'i')
            ->join('i.domRmqId', 'r')
            ->where('i.domSousTypeDocumentId = :sousTypeDoc')
            ->andWhere('r.description = :rmqDescription')
            ->setParameter('sousTypeDoc', $sousTypeDocument)
            ->setParameter('rmqDescription', $rmqDescription)
            ->getQuery()
            ->getResult();

        $form->add('categorie', EntityType::class, [
            'label' => 'Catégorie',
            'class' => DomCategorie::class,
            'choice_label' => 'description',
            'choices' => $categories,
            'placeholder' => 'Sélectionnez une catégorie',
        ]);
    }

    private function findAllowedTypesQueryBuilder()
    {
        return function (DomSousTypeDocumentRepository $repo) {
            return $repo->createQueryBuilder('s')
                ->where('s.codeSousType NOT IN (:excludedCodes)')
                ->setParameter('excludedCodes', self::EXCLUDED_CODE_SOUS_TYPE_DOC);
        };
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DomFirstFormData::class,
            'attr' => ['id' => 'dom_form'],
        ]);
    }
}
