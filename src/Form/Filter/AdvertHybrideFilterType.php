<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Model\AdvertHybrideSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertHybrideFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', AdvertTypeFilterType::class, [
                'choices' => [
                    'Vente' => Advert::TYPE_OFFER,
                    'Recherche' => Advert::TYPE_RESEARCH,
                    'Troc' => Advert::TYPE_EXCHANGE,
                    'Don' => Advert::TYPE_DON
                ]
            ])
            ->add('urgent', UrgentFilterType::class)
            ->add('city', CityFilterType::class)
            ->add('price', PriceFilterType::class, ['required' => false, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdvertHybrideSearch::class,
            'csrf_protection' => false,
            'method' => 'GET'
        ]);
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}
