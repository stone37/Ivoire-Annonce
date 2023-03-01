<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Entity\Category;
use App\Model\EmploisSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmploisFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Category $category */
        $category = $options['category'];

        $builder
            ->add('type', AdvertTypeFilterType::class, ['choices' => $this->getChoices($category)])
            ->add('urgent', UrgentFilterType::class)
            ->add('city', CityFilterType::class)
            ->add('price', PriceFilterType::class, ['required' => false, 'label' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmploisSearch::class,
            'csrf_protection' => false,
            'method' => 'GET'
        ])->setRequired('category');
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }

    private function getChoices(Category $category): array
    {
        if ($category->getSlug() === 'offres-demploi') {
            return ['Offre' => Advert::TYPE_OFFER];
        }

        if ($category->getSlug() === 'demandes-demploi') {
            return ['Demande' => Advert::TYPE_RESEARCH];
        }

        return [
            'Offre de stage' => Advert::TYPE_OFFER,
            'Recherche de stage' => Advert::TYPE_RESEARCH
        ];
    }
}