<?php

namespace App\Form\Admin\PersonnelUser;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Entity\Admin\PersonnelUser\User; // Import User entity
use App\Entity\Admin\AgenceService\Agence; // Import Agence entity
use App\Entity\Admin\AgenceService\Service; // Import Service entity
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Import EntityType
use App\Entity\Admin\ApplicationGroupe\Permission; // Import Permission entity
use App\Entity\Admin\Historisation\TypeDocument; // Import TypeDocument entity

class UserAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['display_users']) {
            $builder->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname', // Display the fullname of the user
                'multiple' => false,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select',
                    'data-placeholder' => 'Sélectionnez un utilisateur...',
                ],
            ]);
        }

        $builder
            ->add('agence', EntityType::class, [
                'label' => 'Agence cible',
                'class' => Agence::class,
                'choice_label' => function (Agence $agence) {
                    return $agence->getCodeNom(' - ');
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.code', 'ASC');
                },
                'multiple' => false,
                'expanded' => false,
                'required' => false, // Agence can be null if allAgence is true
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select',
                    'data-placeholder' => 'Sélectionnez une agence...',
                ],
            ])
            ->add('service', EntityType::class, [
                'label' => 'Service cible',
                'class' => Service::class,
                'choice_label' => function (Service $service) {
                    return $service->getCodeNom(' - ');
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.code', 'ASC');
                },
                'multiple' => false,
                'expanded' => false,
                'required' => false, // Service can be null if allService is true
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select',
                    'data-placeholder' => 'Sélectionnez un service...',
                ],
            ])
            ->add('allAgence', CheckboxType::class, [
                'label' => 'Toutes les agences',
                'required' => false,
            ])
            ->add('allService', CheckboxType::class, [
                'label' => 'Toutes les services',
                'required' => false,
            ])
            ->add('permissions', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'code',
                'multiple' => true,
                'expanded' => false,
                'group_by' => function ($choice, $key, $value) {
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
            ->add('typeDocument', EntityType::class, [
                'label' => 'Type de document',
                'class' => TypeDocument::class,
                'choice_label' => 'libelleDocument',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select',
                    'data-placeholder' => 'Sélectionnez un type de document...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAccess::class,
            'display_users' => true,
        ]);

        $resolver->setAllowedTypes('display_users', 'bool');
    }
}
