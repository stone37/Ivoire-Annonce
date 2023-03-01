<?php

namespace App\Form;

use App\Entity\Discount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discount', IntegerType::class, ['label' => 'Valeur'])
            ->add('code', TextType::class, ['label' => 'Code de réduction'])
            ->add('utilisation', IntegerType::class, ['label' => 'Nombre d\'utilisation'])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Prix fixe de remise' => Discount::FIXED_DISCOUNT,
                    'Pourcentage de remise' => Discount::PERCENTAGE_DISCOUNT,
                ],
                'attr' => [
                    'class' => 'mdb-select md-outline md-form dropdown-primary',
                ]
            ])
            ->add('enabled', CheckboxType::class, ['label' => 'Activée', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discount::class,
        ]);
    }
}
