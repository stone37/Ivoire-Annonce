<?php

namespace App\Form\Animaux;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;


class ChiensType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vente' => Advert::TYPE_OFFER,
                    'Recherche' => Advert::TYPE_RESEARCH,
                    'Don' => Advert::TYPE_DON
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ])
            ->add('sex', ChoiceType::class, [
                'choices' => [
                    'Male' => 'Male',
                    'Femelle' => 'Femelle'
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Sexe (facultatif)'
            ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}