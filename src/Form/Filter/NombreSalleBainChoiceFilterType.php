<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NombreSalleBainChoiceFilterType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6',
                '7' => '7', '8' => '8', '9' => '9', '10' => '10'
            ],
            'label' => 'Nombre de salle(s) de bain',
            'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
            'placeholder' => 'Nombre de salle(s) de bain',
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
