<?php

namespace App\Form\Electronique;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JeuxVideoEtConsolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vente' => Advert::TYPE_OFFER,
                    'Recherche' => Advert::TYPE_RESEARCH,
                    'Troc ou échange' => Advert::TYPE_EXCHANGE,
                    'Don' => Advert::TYPE_DON
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ])
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Quasi neuf' => 'Quasi neuf',
                    'Occasion' => 'Occasion',
                    'A rénover' => 'A rénover'
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'État du produit'
            ])
            ->add('brand', ChoiceType::class, [
                'choices' => [
                    'Nintendo DS' => 'Nintendo DS', 'Nintendo 3DS' => 'Nintendo 3DS',
                    'Nintendo Switch' => 'Nintendo Switch',
                    'Nintendo Wii' => 'Nintendo Wii', 'Nintendo Wii U' => 'Nintendo Wii U',
                    'PlayStation 5' => 'PlayStation 5', 'PlayStation 4' => 'PlayStation 4',
                    'PlayStation 3' => 'PlayStation 3', 'PlayStation 2' => 'layStation 2',
                    'PlayStation One' => 'PlayStation One', 'PlayStation Vita' => 'PlayStation Vita',
                    'Sony PSP' => 'Sony PSP', 'XBOX One' => 'XBOX One', 'Xbox Series' => 'Xbox Series',
                    'XBOX 360' => 'XBOX 360', 'Autre' => 'Autre'
                ],
                'label' => 'Marque',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Marque'
            ])
            ->add('processing', ChoiceType::class, [
                'choices' => [
                    'Livraison possible' => Advert::DELIVERY_PROCESSING,
                    'Expédition possible' => Advert::SHIPMENT_PROCESSING
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    return ['class' => 'form-check-input filled-in'];
                },
                'label' => 'Traitement (facultatif)',
                'placeholder' => 'Traitement (facultatif)',
                'required' => false,
                'expanded' => true,
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default', 'ACHAT', 'JV']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
