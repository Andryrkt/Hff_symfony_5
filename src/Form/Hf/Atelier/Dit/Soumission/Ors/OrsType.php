<?php

namespace App\Form\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Form\Common\FileUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'numeroDit',
                TextType::class,
                [
                    'label' => 'Numéro DIT',
                    'disabled' => true,
                ]
            )
            ->add(
                'numeroOr',
                TextType::class,
                [
                    'label' => 'Numéro OR *',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'observation',
                TextareaType::class,
                [
                    'label' => 'Observation',
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            )
            ->add(
                'pieceJoint01',
                FileUploadType::class,
                [
                    'label' => 'Veuillez insérer l\'OR à valider *',
                    'required' => false,
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                    'max_size' => '5M'
                ]
            )
            ->add(
                'pieceJoint02',
                FileUploadType::class,
                [
                    'label' => 'Veuillez insérer le devis à fusionner avec l\'OR ',
                    'required' => false,
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                    'max_size' => '5M'
                ]
            )
            ->add(
                'pieceJoint03',
                FileUploadType::class,
                [
                    'label' => 'Veuillez insérer le BC ou autre document à fusionner avec l\'OR (si existant) ',
                    'required' => false,
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                    'max_size' => '5M'
                ]
            )
            ->add(
                'pieceJoint04',
                FileUploadType::class,
                [
                    'label' => 'Veuillez insérer le document à fusionner avec l\'OR (si existant) ',
                    'required' => false,
                    'multiple' => true,
                    'allowed_mime_types' => ['application/pdf'],
                    'accept' => '.pdf',
                    'max_size' => '5M'
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrsDto::class,
        ]);
    }
}
