<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;

final class MaintenanceConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => 'Activé ?',
                'required' => true,
            ])
            ->add('ipAddresses', TextType::class, [
                'label' => 'Adresses Ip (facultatif)',
                'attr' => [
                    'placeholder' => 'Ajouter une ou plusieurs adresses ip séparées par une virgule. Par exemple: 255.255.255.255,192.0.0.255',
                ],
                'empty_data' => '',
                'required' => false,
                'help' => 'Séparées par une virgule, sans espace.'
            ])
            ->add('customMessage', TextareaType::class, [
                'label' => 'Message personnalisé (facultatif)',
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control md-textarea',
                    'rows'  => 5
                ]
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'with_seconds' => true,
                'required' => false,
                'constraints' => [new GreaterThan(['propertyPath' => 'parent.all[startDate].data'])]
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => false,
                'widget' => 'single_text',
                'with_seconds' => true,
                'required' => false,
            ])
        ;
    }
}



