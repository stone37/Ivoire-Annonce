<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertTypeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => $this->getType(),
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_type_choice';
    }

    public  function getType(): array
    {
        return [
            'Offre' => Advert::TYPE_OFFER,
            'Recherche' => Advert::TYPE_RESEARCH,
            'Location' => Advert::TYPE_LOCATION,
            'Troc' => Advert::TYPE_EXCHANGE,
            'Don' => Advert::TYPE_DON
        ];
    }
}
