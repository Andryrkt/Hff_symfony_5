<?php

namespace App\Form\Dom;

use App\Dto\Dom\DomSecondFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomSecondFormType extends AbstractType
{
    // This class can be used to define the second form type for the DOM process.
    // It can include methods to build the form, handle events, and configure options.
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Define the fields and options for the second form here
        // For example:
        // $builder->add('field_name', TextType::class, [...]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DomSecondFormData::class,
        ]);
    }
}