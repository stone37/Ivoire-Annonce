<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => [
                    'Paiement par carte bancaire ou par mobile money' => Commande::CARD_PAYMENT,
                    'Paiement par crédit' => Commande::CREDIT_PAYMENT
                ],
                'label' => false,
                'expanded' => true,
                'multiple' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class
        ]);
    }
}
