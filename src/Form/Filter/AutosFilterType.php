<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Entity\Category;
use App\Model\AutosSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutosFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Category $category */
        $category = $options['category'];

        $builder
            ->add('type', AdvertTypeFilterType::class, ['choices' => $this->getChoices($category)])
            ->add('urgent', UrgentFilterType::class)
            ->add('city', CityFilterType::class)
            ->add('price', PriceFilterType::class, ['required' => false, 'label' => false])
            ->add('marque', BrandChoiceFilterType::class)
            ->add('model', ModelChoiceFilterType::class)
            ->add('autoYear', AutoYearFilterType::class, ['required' => false, 'label' => false])
            ->add('kilo', KiloFilterType::class, ['required' => false, 'label' => false])
            ->add('typeCarburant', TypeCarburantChoiceFilterType::class)
            ->add('autoState', AutoStateChoiceFilterType::class);

        $builder->get('marque')->resetViewTransformers();
        $builder->get('model')->resetViewTransformers();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AutosSearch::class,
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
        if ($category->getSlug() === 'locations-de-vehicules') {
            return [
                'Offre de location' => Advert::TYPE_LOCATION,
                'Recherche de location' => Advert::TYPE_RESEARCH
            ];
        }

        if ($category->getSlug() === 'services-automobiles') {
            return [
                'Offre de service' => Advert::TYPE_OFFER,
                'Recherche de service' => Advert::TYPE_RESEARCH
            ];
        }

        return [
            'Vente' => Advert::TYPE_OFFER,
            'Recherche' => Advert::TYPE_RESEARCH
        ];
    }
}
