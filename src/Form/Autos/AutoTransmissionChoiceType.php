<?php

namespace App\Form\Autos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoTransmissionChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Automatique' => 'Automatique',
                '4 roues motrices (4x4)' => '4 roues motrices (4x4)',
                '4x4' => '4x4',
                'Roues motrices arrière' => 'Roues motrices arrière',
                'Roues motrices avant' => 'Roues motrices avant',
                'Autre' => 'Autre'
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
        return 'app_auto_transmission_choice';
    }
}
