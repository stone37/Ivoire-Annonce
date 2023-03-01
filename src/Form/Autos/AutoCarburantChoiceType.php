<?php

namespace App\Form\Autos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoCarburantChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Diesel' => 'Diesel',
                'Essence' => 'Essence',
                'Électrique' => 'Électrique',
                'Hybride' => 'Hybride',
                'Autre' => 'Autre'
            ],
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_auto_carburant_choice';
    }
}
