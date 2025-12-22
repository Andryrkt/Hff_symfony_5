<?php

namespace App\Form\Hf\Materiel\Casier\Liste;

use Symfony\Component\Form\AbstractType;
use App\Dto\Hf\Materiel\Casier\SearchDto;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Admin\AgenceService\AgenceRepository;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'agence',
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

                ]
            )
            ->add(
                'casier',
                TextType::class,
                [
                    'label' => 'Casier',
                    'required' => false,
                ]
            )
            ->add('statut', EntityType::class, [
                'label' => 'Statut',
                'class' => StatutDemande::class,
                'choice_label' => 'description',
                'query_builder' => function (StatutDemandeRepository $statutDemandeRepository) {
                    return $statutDemandeRepository->createQueryBuilder('a')
                        ->where('a.codeApplication = :codeApplication')
                        ->setParameter('codeApplication', 'CAS')
                        ->orderBy('a.description', 'ASC');
                },
                'required' => false,
                'placeholder' => null,
                'data' => $options['data']->statut
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchDto::class,
        ]);
    }
}
