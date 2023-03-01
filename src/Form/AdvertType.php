<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'annonce',
                'help' => 'Saisissez un titre décrivant précisément le bien.'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'annonce',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 5],
                'help' => 'Décrivez précisément votre bien, en indiquant son état, ses caractéristiques, ainsi tout autre information importante pour l\'acquéreur.'
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix (CFA)',
                'attr' => ['min' => 0],
                'help' => 'Laisser ce champ vide si vous voulez que le prix soit sur demande'
            ])
            ->add('priceState', CheckboxType::class, [
                'label' => 'Prix négociable (facultatif)',
                'required' => false,
            ])
            ->add('location', LocationType::class)
            /*->add('images', CollectionType::class, [
                'entry_type' => AdvertPictureType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])*/;
    }

        public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}

