<?php

namespace App\Form\Hf\Atelier\Dit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SoumissionDocumentAValidationType extends AbstractType
{
    const DOC_A_SOUMETTRE = [
        'DEVIS - Vérification de prix' => 'DEVIS-VP',
        'DEVIS - Validation atelier' => 'DEVIS-VA',
        'BC-BON COMMANDE' => 'BC',
        'OR-ORDRE DE REPARATION' => 'OR',
        'RI-RAPPORT D\'INTERVENTION' => 'RI',
        'FACTURE' => 'FACTURE',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'SoumissionDocumentAValidation',
                ChoiceType::class,
                [
                    'label' => 'Docs à intégrer dans DW',
                    'choices' => self::DOC_A_SOUMETTRE,
                    'placeholder' => '--',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
