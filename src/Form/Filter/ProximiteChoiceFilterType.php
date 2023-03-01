<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProximiteChoiceFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Arrêt de bus' => 'Arrêt de bus',
                'Commerces' => 'Commerces',
                'Gare de taxi' => 'Gare de taxi',
                'Ligne de Gbaka' => 'Ligne de Gbaka',
                'Écoles' => 'Écoles',
                'Espaces verts' => 'Espaces verts'
            ],
            'choice_attr' => function () {
                return ['class' => 'form-check-input filled-in'];
            },
            'label' => 'Proximité',
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
