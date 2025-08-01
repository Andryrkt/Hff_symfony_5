<?php

namespace App\Form\Shared;

use App\Entity\Admin\PersonnelUser\User;
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

        $builder
            ->add('agenceEmetteur', TextType::class, [
                'label' => 'Agence émettrice',
                'disabled' => true,
                'mapped' => false, // Car ce sont des champs affichés mais non liés à l'entité
            ])
            ->add('serviceEmetteur', TextType::class, [
                'label' => 'Service émetteur',
                'disabled' => true,
                'mapped' => false, // Car ce sont des champs affichés mais non liés à l'entité
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var User $user */
            $user = $this->security->getUser();
            if ($user) {
                // dd($user->getAgenceEmetteur(), $user->getServiceEmetteur());
                $form->get('agenceEmetteur')->setData($user->getAgenceEmetteur());
                $form->get('serviceEmetteur')->setData($user->getServiceEmetteur());
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
