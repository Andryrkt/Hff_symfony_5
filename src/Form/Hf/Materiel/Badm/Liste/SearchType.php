<?php

namespace App\Form\Hf\Materiel\Badm\Liste;

use App\Form\Common\DateRangeType;
use App\Form\Common\AgenceServiceType;
use App\Dto\Hf\Materiel\Badm\SearchDto;
use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statut', EntityType::class, [
                'label' => 'Statut',
                'class' => StatutDemande::class,
                'choice_label' => 'description',
                'placeholder' => '-- Choisir un statut --',
                'required' => false,
                'query_builder' => function (StatutDemandeRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.codeApplication = :codeApp')
                        ->setParameter('codeApp', 'BDM');
                },
            ])
            ->add('idMateriel', TextType::class, [
                'label' => 'Id Materiel',
                'required' => false,
            ])
            ->add('numParc', TextType::class, [
                'label' => "N° Parc",
                'required' => false
            ])
            ->add('numSerie', TextType::class, [
                'label' => "N° Serie",
                'required' => false
            ])
            ->add('numeroBadm', TextType::class, [
                'label' => "N° BADM",
                'required' => false
            ])
            ->add('typeMouvement', EntityType::class, [
                'label' => 'Type Mouvement',
                'class' => TypeMouvement::class,
                'choice_label' => 'description',
                'placeholder' => '-- Choisir une type de mouvement--',
                'required' => false,
            ])
            ->add('dateDemande', DateRangeType::class, [
                'label' => false,
                'debut_label' => 'Date début demande',
                'fin_label' => 'Date fin demande',
                'with_time' => false,
            ])
            ->add('emetteur', AgenceServiceType::class, [
                'render_type' => 'hidden',
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
                'render_type' => 'hidden',
                'label' => false,
                'required' => false,
                'mapped' => true,
                'agence_label' => 'Agence Destinataire',
                'service_label' => 'Service Destinataire',
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
        $resolver->setDefined('idAgenceEmetteur');
    }
}
