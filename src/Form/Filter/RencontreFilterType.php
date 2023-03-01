<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Model\RencontreSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RencontreFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', AdvertTypeFilterType::class, [
                'choices' => ['Offre' => Advert::TYPE_OFFER]
            ])
            ->add('urgent', UrgentFilterType::class)
            ->add('city', CityFilterType::class)
            ->add('price', PriceFilterType::class, ['required' => false, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RencontreSearch::class,
            'csrf_protection' => false,
            'method' => 'GET'
        ])->setRequired('category');
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}