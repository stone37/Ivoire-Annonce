<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrandChoiceFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [],
            'label' => 'Marque',
            'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary advert-brand-field'],
            'placeholder' => 'Marque',
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
