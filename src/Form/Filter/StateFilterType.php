<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StateFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Neuf' => 'Neuf',
                'Quasi neuf' => 'Quasi neuf',
                'Occasion' => 'Occasion',
                'A rénover' => 'A rénover'
            ],
            'label' => 'État du produit',
            'required' => false,
            'multiple' => false,
            'expanded' => true,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
