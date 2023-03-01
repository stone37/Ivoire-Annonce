<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Entity\Category;
use App\Model\AnimauxSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimauxFilterType extends AbstractType
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
            'data_class' => AnimauxSearch::class,
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
        if ($category->getSlug() === 'chiens' || $category->getSlug() === 'chats') {
            return [
                'Vente' => Advert::TYPE_OFFER,
                'Recherche' => Advert::TYPE_RESEARCH,
                'Don' => Advert::TYPE_DON
            ];
        }

        if ($category->getSlug() === 'services-pour-animaux') {
            return [
                'Offre de service' => Advert::TYPE_OFFER,
                'Recherche de service' => Advert::TYPE_RESEARCH
            ];
        }

        if ($category->getSlug() === 'animaux-perdus-trouves') {
            return [
                'Animaux retrouvés' => Advert::TYPE_OFFER,
                'Animaux perdus' => Advert::TYPE_RESEARCH
            ];
        }

        return [
            'Vente' => Advert::TYPE_OFFER,
            'Recherche' => Advert::TYPE_RESEARCH
        ];
    }
}