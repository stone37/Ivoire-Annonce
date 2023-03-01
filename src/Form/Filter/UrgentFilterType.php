<?php

namespace App\Form\Filter;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrgentFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => 'Annonce <span>urgente</span>',
            'label_html' => true,
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return CheckboxType::class;
    }
}
