<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InteriorChoiceFilterType extends AbstractFilterType
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
            'choice_attr' => function () {
                return ['class' => 'form-check-input filled-in'];
            },
            'label' => 'Intérieur',
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
