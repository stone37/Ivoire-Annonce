<?php

namespace App\Form\Electronique;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TelephonesMobilesEtSmartphonesType extends AbstractType
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
                    '360' => '360', 'Alcatel' => 'Alcatel', 'Apple' => 'Apple', 'Asus' => 'Asus', 'Realme' => 'Realme',
                    'Blu' => 'Blu', 'Google' => 'Google', 'HiSense' => 'HiSense', 'Hp' => 'Hp', 'InnJoo' => 'InnJoo',
                    'Honor' => 'Honor', 'TCL' => 'TCL', 'CAT' => 'CAT', 'Elephone' => 'Elephone', 'Haier' => 'Haier',
                    'Infinix' => 'Infinix', 'Samsung' => 'Samsung', 'Acer' => 'Acer', 'Blackberry' => 'Blackberry',
                    'Microsoft' => 'Microsoft', 'Itel' => 'Itel', 'LG' => 'LG', 'Nokia' => 'Nokia', 'Techno' => 'Techno',
                    'Sony' => 'Sony', 'Sony Ericsson' => 'Sony Ericsson', 'OPPO' => 'OPPO', 'OnePlus' => 'OnePlus',
                    'Tesla' => 'Tesla', 'Toshiba' => 'Toshiba', 'Lenovo' => 'Lenovo', 'Motorola' => 'Motorola',
                    'Huawei' => 'Huawei', 'HTC' => 'HTC', 'Vivo' => 'Vivo', 'Wiko' => 'Wiko', 'Xiaomi' => 'Xiaomi',
                    'YU' => 'YU', 'ZTE' => 'ZTE', 'Autre' => 'Autre'
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
            'validation_groups' => ['Default', 'ACHAT', 'TELEPHONE']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}

