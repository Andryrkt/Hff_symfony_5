<?php

namespace App\Form\Common;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AgenceServiceType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['render_type'] === 'hidden') {
            $this->buildHiddenForm($builder, $options);
        } else {
            $this->buildSelectForm($builder, $options);
        }
    }

    private function buildHiddenForm(FormBuilderInterface $builder, array $options): void
    {
        $agenceTransformer = new EntityToIdTransformer($this->em, Agence::class);
        $serviceTransformer = new EntityToIdTransformer($this->em, Service::class);

        $builder
            ->add(
                $builder->create('agence', HiddenType::class, [
                    'required' => $options['agence_required'],
                    'attr' => ['class' => $options['agence_class'] ?? 'light-agence-input'],
                ])->addModelTransformer($agenceTransformer)
            )
            ->add(
                $builder->create('service', HiddenType::class, [
                    'required' => $options['service_required'],
                    'attr' => ['class' => $options['service_class'] ?? 'light-service-input'],
                ])->addModelTransformer($serviceTransformer)
            );
    }

    private function buildSelectForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('agence', EntityType::class, [
                'label' => $options['agence_label'],
                'class' => Agence::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('a');
                    if (!empty($options['agence_codes'])) {
                        $qb->where($qb->expr()->in('a.codeAgence', $options['agence_codes']));
                    }
                    return $qb;
                },
                'choice_label' => function (Agence $agence): string {
                    return $agence->getCode() . ' ' . $agence->getNom();
                },
                'placeholder' => $options['agence_placeholder'],
                'required' => $options['agence_required'],
                'disabled' => $options['disabled']
            ]);

        // Pré-set data
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $agence = $data ? $this->getAgenceFromData($data) : null;

            $this->addServiceField($event->getForm(), $agence, $options);
        });

        // Pré-submit
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $data = $event->getData();
            $agence = $this->getAgenceFromFormData($data, $event->getForm());
            $this->addServiceField($event->getForm(), $agence, $options);
        });
    }

    private function addServiceField(FormInterface $form, ?Agence $agence, array $options): void
    {
        $services = $agence ? $agence->getServices() : [];

        $form->add('service', EntityType::class, [
            'label' => $options['service_label'],
            'class' => Service::class,
            'choice_label' => function (Service $service): string {
                return $service->getCode() . ' ' . $service->getNom();
            },
            'placeholder' => $options['service_placeholder'],
            'choices' => $services,
            'required' => $options['service_required'],
            'disabled' => $options['disabled']
        ]);
    }

    private function getAgenceFromData($data): ?Agence
    {
        if (is_object($data) && method_exists($data, 'getAgence')) {
            return $data->getAgence();
        }
        if (is_array($data) && isset($data['agence'])) {
            return $data['agence'];
        }

        return null;
    }

    private function getAgenceFromFormData(array $data, FormInterface $form): ?Agence
    {
        if (isset($data['agence']) && $data['agence']) {
            // Use the injected EntityManager
            return $this->em->getRepository(Agence::class)->find($data['agence']);
        }

        return null;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'render_type' => 'select', // 'select' or 'hidden'
            'agence_label' => "Agence",
            'agence_placeholder' => '-- Choisir une agence--',
            'agence_required' => false,
            'service_label' => "Service",
            'service_placeholder' => '-- Choisir un service--',
            'service_required' => false,
            'agence_codes' => [],
            // Options for hidden mode
            'agence_class' => null,
            'service_class' => null,
            'disabled' => false,
        ]);
    }

    public function finishView(\Symfony\Component\Form\FormView $view, FormInterface $form, array $options): void
    {
        if ($options['render_type'] === 'hidden') {
            $this->addSelectedLabel($view->children['agence'], $form->get('agence')->getData());
            $this->addSelectedLabel($view->children['service'], $form->get('service')->getData());
        }
    }

    private function addSelectedLabel(\Symfony\Component\Form\FormView $view, $data): void
    {
        if (is_object($data) && method_exists($data, 'getCode') && method_exists($data, 'getNom')) {
            $view->vars['selected_label'] = $data->getCode() . ' ' . $data->getNom();
        }
    }
}
