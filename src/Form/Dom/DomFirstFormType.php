<?php

namespace App\Form\Dom;

use App\Dto\Dom\DomFirstFormData;
use App\Entity\Dom\DomRmq;
use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomIndemnite;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Entity\Dom\DemandeOrdreMission;
use App\Entity\Dom\DomSousTypeDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\PersonnelUser\Personnel;
use Symfony\Component\Security\Core\Security;
use App\Form\Shared\AgenceServiceEmetteurType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\Dom\DomSousTypeDocumentRepository;
use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAllowedTypesQueryBuilder()
    {
        return function (DomSousTypeDocumentRepository $repo) {
            return $repo->createQueryBuilder('s')
                ->where('s.codeSousType NOT IN (:excludedCodes)')
                ->setParameter('excludedCodes', self::EXCLUDED_CODE_SOUS_TYPE_DOC);
        };
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
            ->add('salarie', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Salarié',
                'choices' => self::SALARIE,
                'data' => 'PERMANENT',
                'expanded' => true, // Pour afficher comme des boutons radio
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

        // Gestion des événements
        $this->setupEventListeners($builder);
    }

    private function setupEventListeners(FormBuilderInterface $builder): void
    {
        // PRE_SET_DATA
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // Ajout dynamique du champ catégorie
            if ($data->sousTypeDocument) {
                $this->addCategorieField($form, $data->sousTypeDocument, $data->getAgenceEmetteur());
            }

            // Ajout dynamique du champ matriculeNom
            if ($data->getCodeAgenceAutoriser() && $data->getCodeSreviceAutoriser()) {
                $this->addMatriculeNomField($form, $data->getCodeAgenceAutoriser(), $data->getCodeSreviceAutoriser());
            }
        });

        // PRE_SUBMIT
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // Mise à jour dynamique du champ catégorie
            if (isset($data['sousTypeDocument']) && isset($data['agenceEmetteur'])) {
                $sousTypeDocument = $this->entityManager
                    ->getRepository(DomSousTypeDocument::class)
                    ->find($data['sousTypeDocument']);

                $this->addCategorieField($form, $sousTypeDocument, $data['agenceEmetteur']);
            }

            // Gestion du champ matriculeNom pour les salariés permanents
            if (isset($data['salarie']) && $data['salarie'] === 'PERMANENT') {
                $this->addMatriculeNomField($form, $data['codeAgenceAutoriser'] ?? null, $data['codeSreviceAutoriser'] ?? null);
            }
        });
    }

    private function addCategorieField($form, ?DomSousTypeDocument $sousTypeDocument, ?string $agenceEmetteur): void
    {
        $rmqDescription = str_starts_with($agenceEmetteur, '50') ? DomRmq::DESCRIPTION_50 : DomRmq::DESCRIPTION_STD;
        $rmq = $this->entityManager->getRepository(DomRmq::class)->findOneBy(['description' => $rmqDescription]);

        if (!$sousTypeDocument || !$rmq) {
            return;
        }

        $categories = $this->entityManager->getRepository(DomIndemnite::class)
            ->findDistinctCategoriesByCriteria($sousTypeDocument, $rmq);

        $form->add('categorie', EntityType::class, [
            'label' => 'Catégorie',
            'class' => DomCategorie::class,
            'choice_label' => 'description',
            'choices' => $categories,
            'placeholder' => 'Sélectionnez une catégorie',
        ]);
    }

    private function addMatriculeNomField($form, ?string $codeAgence, ?string $codeService): void
    {
        if (!$codeAgence || !$codeService) {
            return;
        }

        $agenceServiceIriumIds = $this->entityManager
            ->getRepository(AgenceServiceIrium::class)
            ->findIdsByCodes($codeAgence, $codeService);

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
            'query_builder' => function (EntityRepository $repository) use ($agenceServiceIriumIds) {
                return $repository->createQueryBuilder('p')
                    ->where('p.agenceServiceIriumId IN (:agenceIps)')
                    ->setParameter('agenceIps', $agenceServiceIriumIds)
                    ->orderBy('p.Matricule', 'ASC');
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DomFirstFormData::class,
            'attr' => ['id' => 'dom_form'], // Ajout d'un ID pour faciliter le JavaScript
        ]);
    }
}
