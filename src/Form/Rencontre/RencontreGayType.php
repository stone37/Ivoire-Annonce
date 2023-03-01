<?php

namespace App\Form\Rencontre;

use App\Entity\Advert;
use App\Form\AdvertType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RencontreGayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Offre' => Advert::TYPE_OFFER
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'annonce',
                'data' => Advert::TYPE_OFFER
            ]);
    }

    public function getParent(): string
    {
        return AdvertType::class;
    }
}