<?php

namespace App\Form\Shared;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AgenceServiceEmetteurType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add('agence', TextType::class, [
                'label' => 'Agence émettrice',
                'disabled' => true,
            ])
            ->add('service', TextType::class, [
                'label' => 'Service émetteur',
                'disabled' => true,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var User */
            $user = $this->security->getUser();

            if ($user) {
                $form->get('agence')->setData($user->getAgenceEmetteur());
                $form->get('service')->setData($user->getServiceEmetteur());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false, // Car ce sont des champs affichés mais non liés à l'entité
        ]);
    }
}
