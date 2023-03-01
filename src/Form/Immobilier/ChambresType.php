<?php

namespace App\Form\Immobilier;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChambresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Location' => Advert::TYPE_LOCATION,
                    'Recherche' => Advert::TYPE_RESEARCH
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce',
                'data' => Advert::TYPE_LOCATION
            ])
            ->add('surface', IntegerType::class, ['label' => 'Surface (m²)'])
            ->add('immobilierState', ImmobilierStateChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'label' => 'État'
            ])
            ->add('access', ImmobilierAccessChoiceType::class, [
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Accès (facultatif)',
                'placeholder' => 'Accès (facultatif)',
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ])
            ->add('standing', ImmobilierStandingChoiceType::class, [
                'label' => 'Standing (facultatif)',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('interior', ImmobilierInteriorChoiceType::class, [
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Intérieur (facultatif)',
                'placeholder' => 'Intérieur (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('serviceInclus', ImmobilierServiceChoiceType::class, [
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Services inclus (facultatif)',
                'placeholder' => 'Services inclus (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('proximite', ImmobilierProximiteChoiceType::class, [
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Proximité (facultatif)',
                'placeholder' => 'Proximité (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default', 'CHAMBRE']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
