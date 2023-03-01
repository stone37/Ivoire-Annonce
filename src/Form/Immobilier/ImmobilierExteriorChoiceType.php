<?php

namespace App\Form\Immobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImmobilierExteriorChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Garage couvert' => 'Garage couvert',
                'Garage extérieure' => 'Garage extérieure',
                'Parking' => 'Parking',
                'Parking couvert' => 'Parking couvert',
                'Piscine' => 'Piscine'
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
        return 'app_immobilier_exterior_choice';
    }
}
