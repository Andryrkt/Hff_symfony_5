<?php

namespace App\Form\Common;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('debut', DateType::class, [
                'widget' => 'single_text',
                'label' => $options['debut_label'],
                'required' => false,
            ])
            ->add('fin', DateType::class, [
                'widget' => 'single_text',
                'label' => $options['fin_label'],
                'required' => false,
            ]);

        // Ajout des champs d'heure si demandé
        if ($options['with_time']) {
            $builder
                ->add('heureDebut', TimeType::class, [
                    'label' => $options['heure_debut_label'],
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control',
                        'value' => $options['heure_debut_default'],
                    ],
                    'input' => 'datetime',
                    'required' => false,
                ])
                ->add('heureFin', TimeType::class, [
                    'label' => $options['heure_fin_label'],
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control',
                        'value' => $options['heure_fin_default'],
                    ],
                    'input' => 'datetime',
                    'required' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'debut_label' => 'Date (début)',
            'fin_label' => 'Date (fin)',
            'heure_debut_label' => 'Heure début',
            'heure_fin_label' => 'Heure fin',
            'heure_debut_default' => '08:00',
            'heure_fin_default' => '18:00',
            'with_time' => false, // Option pour activer/désactiver les champs d'heure
        ]);

        // Validation des options
        $resolver->setAllowedTypes('with_time', 'bool');
        $resolver->setAllowedTypes('heure_debut_default', 'string');
        $resolver->setAllowedTypes('heure_fin_default', 'string');
    }
}
