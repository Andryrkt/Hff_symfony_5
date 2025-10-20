<?php

namespace App\Form\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\Personnel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonnelType extends AbstractType
{
    private const SOCIETE_CHOICES = [
        'HFF' => 'HFF',
        'HFG' => 'HFG',
        'HFT' => 'HFT',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('nom')
            ->add('prenoms')
            ->add('matricule')
            ->add('societe', ChoiceType::class, [
                'choices' => self::SOCIETE_CHOICES,
                'placeholder' => 'Choisir une société',
                'required' => false,
            ])

            ->add('agenceServiceIrium', EntityType::class, [
                'class' => 'App\Entity\Admin\AgenceService\AgenceServiceIrium',
                'choice_label' => 'codeSage',
                'placeholder' => 'Choisir une agence/service',
                'required' => false,
            ])
            // ->add('users', EntityType::class, [
            //     'class' => 'App\Entity\Admin\PersonnelUser\User',
            //     'choice_label' => 'username',
            //     'placeholder' => 'Choisir un utilisateur',
            //     'required' => false,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
        ]);
    }
}
