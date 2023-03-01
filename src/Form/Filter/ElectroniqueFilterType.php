<?php

namespace App\Form\Filter;

use App\Entity\Advert;
use App\Entity\Category;
use App\Model\ElectroniqueSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectroniqueFilterType extends AbstractType
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
            ->add('state', StateFilterType::class)
            ->add('brand', ChoiceType::class, [
                'choices' => [],
                'label' => 'Marque',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary brand-field'],
                'placeholder' => 'Marque',
                'required' => false
            ])
            ->add('processing', ProcessingChoiceFilterType::class);

        $builder->get('brand')->resetViewTransformers();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ElectroniqueSearch::class,
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
        if ($category->getSlug() === 'services-pour-telephones-et-ordinateurs') {
            return [
                'Offre de service' => Advert::TYPE_OFFER,
                'Recherche de service' => Advert::TYPE_RESEARCH
            ];
        }

        return [
            'Vente' => Advert::TYPE_OFFER,
            'Recherche' => Advert::TYPE_RESEARCH,
            'Troc' => Advert::TYPE_EXCHANGE,
            'Don' => Advert::TYPE_DON
        ];
    }
}