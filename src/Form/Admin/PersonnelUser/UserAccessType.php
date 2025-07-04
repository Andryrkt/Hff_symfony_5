<?php

namespace App\Form\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\UserAccess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accessType')
            ->add('users')
            ->add('agence')
            ->add('service')
            ->add('application')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAccess::class,
        ]);
    }
}
