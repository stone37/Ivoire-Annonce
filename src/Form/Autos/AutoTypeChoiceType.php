<?php

namespace App\Form\Autos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Berline' => 'Berline', 'Bicorps' => 'Bicorps', 'Cabriolet' => 'Cabriolet',
                'Camionnette' => 'Camionnette', 'Coupé (2 portes)' => 'Coupé (2 portes)',
                'Familiale' => 'Familiale', 'Fourgonnette' => 'Fourgonnette', 'Fourgon' => 'Fourgon',
                'SUV' => 'SUV', 'Autre' => 'Autre'
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
        return 'app_auto_type_choice';
    }
}
