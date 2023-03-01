<?php

namespace App\Form\Immobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImmobilierCuisineChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Indépendante' => 'Indépendante',
                'Américaine' => 'Américaine',
                'Coin cuisine' => 'Coin cuisine',
                'Équipée' => 'Équipée',
                'Meublé' => 'Meublé',
                'Intégrée' => 'Intégrée'
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
        return 'app_immobilier_cuisine_choice';
    }
}
