<?php

namespace App\Form\Hf\Materiel\Badm\Creation;

use Symfony\Component\Form\AbstractType;
use App\Dto\Hf\Materiel\Badm\FirstFormDto;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FirstFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ->add('typeMouvement', EntityType::class, [
                'class' => TypeMouvement::class,
                'choice_label' => 'description',
                'label' => 'Type Mouvement',
                'placeholder' => false,
                'required' => true,
                'data' => $options["data"]->typeMouvement ?? null
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FirstFormDto::class,
        ]);
    }
}
