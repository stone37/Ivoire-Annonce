<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Entity\Category;
use App\Model\ImmobilierSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImmobilierFilterType extends AbstractType
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
            ->add('surface', SurfaceFilterType::class, ['required' => false, 'label' => false])
            ->add('nombrePiece', NombrePieceChoiceFilterType::class)
            ->add('nombreChambre', NombreChambreChoiceFilterType::class)
            ->add('nombreSalleBain', NombreSalleBainChoiceFilterType::class)
            ->add('immobilierState', ImmobilierStateChoiceFilterType::class)
            ->add('access',AccessChoiceFilterType::class)
            ->add('proximite', ProximiteChoiceFilterType::class)
            ->add('interior', InteriorChoiceFilterType::class)
            ->add('exterior', ExteriorChoiceFilterType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ImmobilierSearch::class,
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
        if ($category->getSlug() === 'colocations' or
            $category->getSlug() === 'chambres') {
            return [
                'Location' => Advert::TYPE_LOCATION,
                'Recherche' => Advert::TYPE_RESEARCH
            ];
        }

        if ($category->getSlug() === 'services-immobiliers') {
            return [
                'Offre de service' => Advert::TYPE_OFFER,
                'Recherche de service' => Advert::TYPE_RESEARCH
            ];
        }

        return [
            'Vente' => Advert::TYPE_OFFER,
            'Location' => Advert::TYPE_LOCATION,
            'Recherche' => Advert::TYPE_RESEARCH
        ];
    }
}