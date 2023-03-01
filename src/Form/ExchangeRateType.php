<?php

namespace App\Form;

use App\Entity\ExchangeRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExchangeRateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ratio', NumberType::class, [
                'label' => 'Ratio',
                'invalid_message' => 'Le rapport doit être un nombre',
                'scale' => 5,
                'required' => true
            ])
            ->add('sourceCurrency', CurrencyChoiceType::class, [
                'label' => 'Devise d\'origine',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Devise d\'origine',
                'required' => true
            ])
            ->add('targetCurrency', CurrencyChoiceType::class, [
                'label' => 'Devise cible',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Devise cible',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExchangeRate::class,
        ]);
    }
}
