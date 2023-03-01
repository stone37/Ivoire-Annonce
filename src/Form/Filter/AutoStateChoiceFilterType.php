<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoStateChoiceFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Irréprochable' => 'Irréprochable',
                'Bon' => 'Bon',
                'Moyen' => 'Moyen',
                'Prévoir entretien' => 'Prévoir entretien',
                'Accidenté' => 'Accidenté'
            ],
            'label' => 'État de véhicule',
            'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
            'placeholder' => 'État de véhicule',
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
