<?php

namespace App\Form\Immobilier;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerrainsAgricolesType extends AbstractType
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
            ->add('surface', IntegerType::class, ['label' => 'Superficie (ha)']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['Default', 'TERRAIN']
        ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}
