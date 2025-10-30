<?php

namespace App\Form\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\UserAccess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Import EntityType
use App\Entity\Admin\PersonnelUser\User; // Import User entity
use App\Entity\Admin\AgenceService\Agence; // Import Agence entity
use App\Entity\Admin\AgenceService\Service; // Import Service entity
use App\Entity\Admin\ApplicationGroupe\Permission; // Import Permission entity

class UserAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname', // Display the fullname of the user
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('agence', EntityType::class, [
                'class' => Agence::class,
                'choice_label' => 'nom', // Display the name of the agency
                'multiple' => false,
                'expanded' => false,
                'required' => false, // Agence can be null if allAgence is true
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'choice_label' => 'nom', // Display the name of the service
                'multiple' => false,
                'expanded' => false,
                'required' => false, // Service can be null if allService is true
            ])
            ->add('allAgence')
            ->add('allService')
            ->add('permissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'name', // Display the name of the permission
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAccess::class,
        ]);
    }
}
