<?php

namespace App\Form\Immobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImmobilierInteriorChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Aire conditionné' => 'Aire conditionné',
                'Salle de jeux' => 'Salle de jeux',
                'Véranda' => 'Véranda',
                'Buanderie/lingerie' => 'Buanderie/lingerie',
                'Double vitrage' => 'Double vitrage',
                'Isolation acoustique' => 'Isolation acoustique',
                'Câble/Télé' => 'Câble/Télé',
                'Wi-Fi' => 'Wi-Fi'
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
        return 'app_immobilier_interior_choice';
    }
}
