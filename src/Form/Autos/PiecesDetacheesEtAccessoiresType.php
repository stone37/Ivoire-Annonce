<?php

namespace App\Form\Autos;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PiecesDetacheesEtAccessoiresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Vente' => Advert::TYPE_OFFER,
                    'Recherche' => Advert::TYPE_RESEARCH
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ])
            ->add('marque', AutoBrandChoiceType::class, [
                'label' => 'Marque (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Marque',
                'required' => false
            ])
            ->add('model', TextType::class, ['label' => 'Modèle (facultatif)', 'required' => false])
            ->add('autoColor', AutoColorChoiceType::class, [
                'label' => 'Couleur (facultatif)',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Couleur',
                'required' => false
            ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
