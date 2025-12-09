<?php

namespace App\Form\Common;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Form\DataTransformer\EntityToIdTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LightAgenceServiceType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $agenceTransformer = new EntityToIdTransformer($this->em, Agence::class);
        $serviceTransformer = new EntityToIdTransformer($this->em, Service::class);

        $builder
            ->add(
                $builder->create('agence', HiddenType::class, [
                    'required' => $options['agence_required'],
                    'attr' => ['class' => $options['agence_class'] ?? 'light-agence-input'], // For JS targeting
                ])->addModelTransformer($agenceTransformer)
            )
            ->add(
                $builder->create('service', HiddenType::class, [
                    'required' => $options['service_required'],
                    'attr' => ['class' => $options['service_class'] ?? 'light-service-input'], // For JS targeting
                ])->addModelTransformer($serviceTransformer)
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'agence_label' => "Agence",
            'agence_placeholder' => '-- Choisir une agence--',
            'agence_required' => false,
            'service_label' => "Service",
            'service_placeholder' => '-- Choisir un service--',
            'service_required' => false,
            'agence_codes' => [],
            // Extra classes for JS hooks
            'agence_class' => null,
            'service_class' => null,
        ]);
    }
}
