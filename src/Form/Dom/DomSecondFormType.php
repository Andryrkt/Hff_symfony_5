<?php

namespace App\Form\Dom;

use App\Dto\Dom\DomSecondFormData;
use Symfony\Component\Form\AbstractType;
use App\Form\Shared\AgenceServiceDebiteurType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomSecondFormType extends AbstractType
{
    const OUI_NON = [
        'NON' => 'NON',
        'OUI' => 'OUI'
    ];
    const DEVISE = [
        'MGA' => 'MGA',
        'EUR' => 'EUR',
        'USD' => 'USD'
    ];

    const MODE_PAYEMENT = [
        'MOBILE MONEY' => 'MOBILE MONEY',
        'ESPECES' => 'ESPECES',
        'VIREMENT BANCAIRE' => 'VIREMENT BANCAIRE',
    ];



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Champ agence
        $builder
            ->add('debiteur', AgenceServiceDebiteurType::class, [
                'label' => 'Débiteur',
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DomSecondFormData::class,
        ]);
    }
}
