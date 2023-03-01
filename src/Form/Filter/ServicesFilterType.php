<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Model\ServicesSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicesFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', AdvertTypeFilterType::class, [
                'choices' => [
                    'Offre de service' => Advert::TYPE_OFFER,
                    'Recherche de service' => Advert::TYPE_RESEARCH
                ]
            ])
            ->add('urgent', UrgentFilterType::class)
            ->add('city', CityFilterType::class)
            ->add('price', PriceFilterType::class, ['required' => false, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServicesSearch::class,
            'csrf_protection' => false,
            'method' => 'GET'
        ])->setRequired('category');
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}