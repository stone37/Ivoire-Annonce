<?php

namespace App\Form\Immobilier;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaisonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vente' => Advert::TYPE_OFFER,
                    'Location' => Advert::TYPE_LOCATION,
                    'Recherche' => Advert::TYPE_RESEARCH
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ])
            ->add('surface', IntegerType::class, ['label' => 'Surface (m²)'])
            ->add('nombrePiece', ImmobilierNombrePieceChoiceType::class, [
                'label' => 'Nombre de pièces',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de pièces'
            ])
            ->add('nombreChambre', IntegerType::class, ['label' => 'Nombre de chambre(s)'])
            ->add('nombreSalleBain', IntegerType::class, ['label' => 'Nombre de salle(s) de bain'])
            ->add('immobilierState', ImmobilierStateChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'label' => 'État'
            ])
            ->add('dateConstruction', ImmobilierYearChoiceType::class, [
                'label' => 'Date de construction (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Date de construction',
                'required' => false,
            ])
            ->add('nombrePlacard', IntegerType::class, ['label' => 'Nombre de placard(s) (facultatif)', 'required' => false])
            ->add('standing', ImmobilierStandingChoiceType::class, [
                'label' => 'Standing (facultatif)',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('cuisine', ImmobilierCuisineChoiceType::class, [
                'label' => 'Cuisine (facultatif)',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('serviceInclus', ImmobilierServiceChoiceType::class, [
                'choice_attr' => function() {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Services inclus (facultatif)',
                'placeholder' => 'Services inclus (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('exterior', ImmobilierExteriorChoiceType::class, [
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Caractéristiques extérieures (facultatif)',
                'placeholder' => 'Caractéristiques extérieures (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
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
            'validation_groups' => ['Default']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
