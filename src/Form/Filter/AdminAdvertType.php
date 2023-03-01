<?php

namespace App\Form\Filter;

use App\Form\AdvertTypeChoiceType;
use App\Form\CategoryHasParentChoiceType;
use App\Form\CategoryParentChoiceType;
use App\Form\CityChoiceType;
use App\Model\Admin\AdvertSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminAdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', CategoryParentChoiceType::class, [
                'label' => 'Catégories',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Catégories',
                'required' => false
            ])
            ->add('subCategory', CategoryHasParentChoiceType::class, [
                'label' => 'Sous catégories',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Sous catégories',
                'required' => false
            ])
            ->add('advertType', AdvertTypeChoiceType::class, [
                'label' => 'Type',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type',
                'required' => false,
            ])
            ->add('city', CityChoiceType::class, [
                'label' => 'Ville',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Ville',
                'required' => false,
            ])
            ->add('reference', TextType::class, ['label' => 'Reference', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdvertSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}