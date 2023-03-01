<?php

namespace App\Form\Autos;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VoituresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vente de véhicule' => Advert::TYPE_OFFER,
                    'Recherche de véhicule' => Advert::TYPE_RESEARCH
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ])
            ->add('marque', AutoBrandChoiceType::class, [
                'label' => 'Marque',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary advert-auto-brand'],
                'placeholder' => 'Marque'
            ])
            ->add('model', ChoiceType::class, [
                'choices' => [],
                'label' => 'Modèle',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary advert-auto-model'],
                'placeholder' => 'Modèle'
            ])
            ->add('autoType', AutoTypeChoiceType::class, [
                'label' => 'Type de véhicule',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type de véhicule'
            ])
            ->add('autoState', AutoStateChoiceType::class, [
                'label' => 'État de véhicule',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'État de véhicule'
            ])
            ->add('autoYear', AutoYearChoiceType::class, [
                'label' => 'Année',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Année',
                'help' => 'Indiquer l\'année de mise en circulation'
            ])
            ->add('kilometrage', IntegerType::class, ['label' => 'Kilométrage (km)'])
            ->add('boiteVitesse', AutoBoiteVitesseChoiceType::class, [
                'label' => 'Boite à vitesse (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Boite à vitesse',
                'required' => false
            ])
            ->add('transmission', AutoTransmissionChoiceType::class, [
                'label' => 'Transmission (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Transmission',
                'required' => false
            ])
            ->add('typeCarburant', AutoCarburantChoiceType::class, [
                'label' => 'Type de carburant (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Type de carburant',
                'required' => false
            ])
            ->add('autoColor', AutoColorChoiceType::class, [
                'label' => 'Couleur (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Couleur',
                'required' => false
            ])
            ->add('nombrePorte', ChoiceType::class, [
                'choices' => ['2' => '2', '3' => '3', '4' => '4', '5' => '5', 'Autre' => 'Autre'],
                'label' => 'Nombre de portes (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de portes',
                'required' => false
            ])
            ->add('nombrePlace', ChoiceType::class, [
                'choices' => ['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', 'Autre' => 'Autre'],
                'label' => 'Nombre de places (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Nombre de places',
                'required' => false
            ])
            ->add('autreInformation', ChoiceType::class, [
                'choices' => [
                    'Première main' => 'Première main',
                    'Véhicule non fumeur' => 'Véhicule non fumeur',
                    'Stationne dans un garage' => 'Stationne dans un garage',
                    'Barres de toit' => 'Barres de toit',
                    'Toit ouvrant' => 'Toit ouvrant',
                    'Jantes en alliage' => 'Jantes en alliage',
                    'Système de navigation' => 'Système de navigation',
                    'Bluetooth' => 'Bluetooth',
                    'Aide au stationnement' => 'Aide au stationnement',
                    'Régulateur de vitesse' => 'Régulateur de vitesse',
                    'Attache-remorque' => 'Attache-remorque',
                    'Air conditionné' => 'Air conditionné'
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Autres informations (facultatif)',
                'placeholder' => 'Autres informations (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ]);

        $builder->get('model')->resetViewTransformers();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default', 'VOITURE']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
