<?php

namespace App\Form\Admin\AgenceService;

use App\Entity\Admin\AgenceService\AgenceServiceIrium;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgenceServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('responsable')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('agence')
            ->add('service')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AgenceServiceIrium::class,
        ]);
    }
}
