<?php

namespace App\Form\Immobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImmobilierProximiteChoiceType extends AbstractType
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
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_immobilier_access_choice';
    }
}
