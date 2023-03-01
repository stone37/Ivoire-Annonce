<?php

namespace App\Form\Autos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoColorChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Argent' => 'Argent',
                'Beige' => 'Beige',
                'Blanc' => 'Blanc',
                'Blanc cassé' => 'Blanc cassé',
                'Bleu clair' => 'Bleu clair',
                'Bleu foncé' => 'Bleu foncé',
                'Bordeaux' => 'Bordeaux',
                'Brun' => 'Brun',
                'Gris clair' => 'Gris clair',
                'Gris foncé' => 'Gris foncé',
                'Havane' => 'Havane',
                'Ivoire' => 'Ivoire',
                'Jaune' => 'Jaune',
                'Marron' => 'Marron',
                'Mauve' => 'Mauve',
                'Noir' => 'Noir',
                'Or' => 'Or',
                'Orange' => 'Orange',
                'Rose' => 'Rose',
                'Rouge' => 'Rouge',
                'Sarcelle' => 'Sarcelle',
                'Vert clair' => 'Vert clair',
                'Vert foncé' => 'Vert foncé',
                'Violet' => 'Violet',
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
        return 'app_auto_boite_vitesse_choice';
    }
}
