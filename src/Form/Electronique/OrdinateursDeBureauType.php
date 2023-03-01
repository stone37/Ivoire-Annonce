<?php

namespace App\Form\Electronique;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdinateursDeBureauType extends AbstractType
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
                    'Acer' => 'Acer', 'Apple' => 'Apple', 'Asus' => 'Asus', 'Compaq' => 'Compaq',
                    'Dell' => 'Dell', 'Fujitsu' => 'Fujitsu', 'Gateway' => 'Gateway', 'Gigabyte' => 'Gigabyte',
                    'Huawei' => 'Huawei', 'Hp' => 'Hp', 'IBM' => 'IBM', 'Intel' => 'Intel', 'Lenovo' => 'Lenovo',
                    'Medion' => 'Medion', 'Microsoft' => 'Microsoft', 'ORDISSIMO' => 'ORDISSIMO', 'Razer' => 'Razer',
                    'Samsung' => 'Samsung', 'Toshiba' => 'Toshiba', 'Sony' => 'Sony', 'MSI' => 'MSI', 'Autre' => 'Autre'
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
            'validation_groups' => ['Default', 'ACHAT', 'ORDINATEUR_BUREAU']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
