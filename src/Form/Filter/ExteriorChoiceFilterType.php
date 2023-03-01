<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExteriorChoiceFilterType extends AbstractFilterType
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
            'choice_attr' => function () {
                return ['class' => 'form-check-input filled-in'];
            },
            'label' => 'Extérieure',
            'expanded' => true,
            'multiple' => true,
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
