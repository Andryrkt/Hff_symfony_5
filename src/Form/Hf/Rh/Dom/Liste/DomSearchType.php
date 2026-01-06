<?php

namespace App\Form\Hf\Rh\Dom\Liste;


use App\Form\Common\DateRangeType;
use App\Dto\Hf\Rh\Dom\DomSearchDto;
use App\Form\Common\AgenceServiceType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\Statut\StatutDemande;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



class DomSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
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
            ->add('sousTypeDocument', EntityType::class, [
                'label'        => 'Sous Type',
                'class'        => SousTypeDocument::class,
                'choice_label' => 'codeSousType',
                'placeholder'  => '-- Choisir un type --',
                'required'     => false,
            ])
            ->add('numDom', TextType::class, [
                'label'    => "N° DOM",
                'required' => false
            ])
            ->add('matricule', TextType::class, [
                'label'    => 'Matricule',
                'required' => false,
            ])
            ->add('dateDemande', DateRangeType::class, [
                'label' => false,
                'debut_label' => 'Date début demande',
                'fin_label' => 'Date fin demande',
                'with_time' => false,
            ])
            ->add('dateMission', DateRangeType::class, [
                'label' => false,
                'debut_label' => 'Date début mission',
                'fin_label' => 'Date fin mission',
                'with_time' => false,
                'required' => false,
            ])
            ->add('pieceJustificatif', ChoiceType::class, [
                'label'       => 'Pièce à jusitifier',
                'placeholder' => '-- Choisir le choix --',
                'choices'     => ['NON' => false, 'OUI' => true],
                'required'    => false
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
                'agence_label' => 'Agence Débiteur',
                'service_label' => 'Service Débiteur',
                'agence_placeholder' => '-- Agence Débiteur --',
                'service_placeholder' => '-- Service Débiteur --',
                'agence_class' => 'agenceDebiteur',
                'service_class' => 'serviceDebiteur',
            ])
            // ->add('limit', ChoiceType::class, [
            //     'label' => 'Résultats par page',
            //     'choices' => [
            //         '10' => 10,
            //         '25' => 25,
            //         '50' => 50,
            //         '100' => 100,
            //     ],
            //     'required' => false,
            //     'attr' => ['class' => 'form-control-sm'],
            // ])
            // ->add('sortBy', HiddenType::class, [
            //     'required' => false,
            // ])
            // ->add('sortOrder', HiddenType::class, [
            //     'required' => false,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DomSearchDto::class,
        ]);
        $resolver->setDefined('idAgenceEmetteur');
    }
}
