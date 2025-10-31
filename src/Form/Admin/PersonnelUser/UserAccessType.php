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
                'choice_label' => 'code',
                'multiple' => true,
                'expanded' => false,
                'group_by' => function($choice, $key, $value) {
                    // Grouper par vignette
                    $vignette = $choice->getVignette();
                    return $vignette ? $vignette->getNom() : 'Sans vignette'; // Adaptez getNom() selon votre entité Vignette
                },
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select',
                    'data-placeholder' => 'Sélectionnez des permissions...',
                ],
                'query_builder' => function ($repository) {
                    // Optionnel : trier les permissions
                    return $repository->createQueryBuilder('p')
                        ->leftJoin('p.vignette', 'v')
                        ->orderBy('v.nom', 'ASC') // Adaptez selon votre entité Vignette
                        ->addOrderBy('p.code', 'ASC');
                },
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
