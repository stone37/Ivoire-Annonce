<?php

namespace App\Form\Autos;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotosEtScootersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vente de moto' => Advert::TYPE_OFFER,
                    'Recherche de moto' => Advert::TYPE_RESEARCH
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ])
            ->add('marque', AutoMotoBrandChoiceType::class, [
                'label' => 'Marque',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Marque'
            ])
            ->add('model', TextType::class, ['label' => 'Modèle (facultatif)', 'required' => false])
            ->add('autoState', AutoStateChoiceType::class, [
                'label' => 'État de véhicule',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'État de véhicule'
            ])
            ->add('autoYear', AutoYearChoiceType::class, [
                'label' => 'Année (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Année',
                'help' => 'Indiquer l\'année de mise en circulation',
                'required' => false
            ])
            ->add('kilometrage', IntegerType::class, ['label' => 'Kilométrage (km) (facultatif)', 'required' => false,])
            ->add('cylindre', IntegerType::class, ['label' => 'Cylindrée (cm3) (facultatif)', 'required' => false,])
            ->add('autoColor', AutoColorChoiceType::class, [
                'label' => 'Couleur (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Couleur',
                'required' => false
            ])
            ->add('typeCarburant', ChoiceType::class, [
                'choices' => [
                    'Essence 2 temps' => 'Essence 2 temps',
                    'Essence 4 temps' => 'Essence 4 temps',
                    'Diesel' => 'Diesel',
                    'Électrique' => 'Électrique',
                    'Autre' => 'Autre'
                ],
                'label' => 'Type de carburant (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type de carburant',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default', 'MOTO']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
