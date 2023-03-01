<?php

namespace App\Form\Animaux;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AnimauxPerdusTrouvesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Animaux retrouvés' => Advert::TYPE_OFFER,
                    'Animaux perdus' => Advert::TYPE_RESEARCH
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce'
            ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}