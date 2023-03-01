<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessChoiceFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Ascenseur' => 'Ascenseur',
                'Digicode' => 'Digicode',
                'Interphone' => 'Interphone',
                'Vidéophone' => 'Vidéophone',
                'Concierge' => 'Concierge'
            ],
            'choice_attr' => function () {
                return ['class' => 'form-check-input filled-in'];
            },
            'label' => 'Accès',
            'expanded' => true,
            'multiple' => true,
            'required' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
