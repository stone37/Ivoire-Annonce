<?php

namespace App\Form\Filter;

use App\Repository\CityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityFilterType extends AbstractFilterType
{
    public function __construct(private CityRepository $cityRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => $this->cityRepository->getWithData(),
            'label' => 'Ville',
            'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
            'placeholder' => 'Ville',
            'required' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
