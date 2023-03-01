<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessingChoiceFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Livraison possible' => Advert::DELIVERY_PROCESSING,
                'Expédition possible' => Advert::SHIPMENT_PROCESSING
            ],
            'choice_attr' => function () {
                return ['class' => 'form-check-input filled-in'];
            },
            'label' => 'Traitement',
            'expanded' => true,
            'multiple' => true,
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
