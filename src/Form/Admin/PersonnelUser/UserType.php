<?php

namespace App\Form\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Group;
use App\Entity\Admin\ApplicationGroupe\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('fullname', TextType::class, [
                'label' => 'Nom complet',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'required' => false,
            ])
            ->add('numero_telephone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
            ])
            ->add('poste', TextType::class, [
                'label' => 'Poste',
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('permissionsDirectes', EntityType::class, [
                'class' => Permission::class,
                'choice_label' => 'code',
                'label' => 'Permissions directes',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'group_by' => function ($choice, $key, $value) {
                    $vignette = $choice->getVignette();
                    return $vignette ? $vignette->getNom() : 'Sans vignette';
                },
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'tom-select',
                    'data-placeholder' => 'Sélectionnez des permissions...',
                ],
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('p')
                        ->leftJoin('p.vignette', 'v')
                        ->orderBy('v.nom', 'ASC')
                        ->addOrderBy('p.code', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
