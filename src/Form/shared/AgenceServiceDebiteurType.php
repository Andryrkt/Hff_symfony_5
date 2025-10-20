<?php

namespace App\Form\shared;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\Admin\AgenceService\AgenceRepository;
use Symfony\Component\Form\AbstractType;

class AgenceServiceDebiteurType extends AbstractType
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('agenceDebiteur', EntityType::class, [
            'label' => 'Agence Débiteur',
            'placeholder' => '-- Choisir une agence Débiteur --',
            'class' => Agence::class,
            'choice_label' => fn(Agence $agence) => sprintf('%s - %s', $agence->getCode(), $agence->getNom()),
            'required' => false,
            'query_builder' => fn(AgenceRepository $repo) => $repo->createQueryBuilder('a')
                ->orderBy('a.code', 'ASC'),
            'attr' => [
                'class' => 'select2'
            ],
        ]);

        $this->setupServiceField($builder);
    }

    private function setupServiceField(FormBuilderInterface $builder): void
    {
        $addServiceField = function ($form, ?Agence $agence = null) {
            $form->add('serviceDebiteur', EntityType::class, [
                'label' => 'Service Débiteur',
                'class' => Service::class,
                'choice_label' => fn(Service $service) => sprintf('%s - %s', $service->getCode(), $service->getNom()),
                'choices' => $agence ? $agence->getServices() : [],
                'required' => false,
                'placeholder' => $agence ? '-- Choisir un service --' : '-- Sélectionnez d\'abord une agence --',
                'attr' => [
                    'class' => 'select2',
                    'disabled' => !$agence,
                ],
            ]);
        };

        // Initialisation du champ service à l'affichage
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($addServiceField) {
            $data = $event->getData();
            $agence = $data && method_exists($data, 'getAgenceDebiteur') ? $data->getAgenceDebiteur() : null;
            $addServiceField($event->getForm(), $agence);
        });

        // Mise à jour du champ service à la soumission
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($addServiceField) {
            $data = $event->getData();
            $agenceId = $data['agenceDebiteur'] ?? null;

            $agence = $agenceId ? $this->em->getRepository(Agence::class)->find($agenceId) : null;
            $addServiceField($event->getForm(), $agence);
        });
    }
}
