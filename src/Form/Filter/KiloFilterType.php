<?php

namespace App\Form\Filter;

use App\PropertyNameResolver\KiloNameResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;

class KiloFilterType extends AbstractFilterType
{
    public function __construct(private KiloNameResolver $kiloNameResolver)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add($this->kiloNameResolver->resolveMinKiloName(), IntegerType::class, [
                'label' => 'Min',
                'required' => false,
                'attr' => ['class' => 'advert_filter_kilo_min', 'placeholder' => 'ex: 10000'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le kilométrage minimum doit être un kilomètre valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le kilométrage minimum ne peut pas être négatif',
                    ]),
                ],
            ])
            ->add($this->kiloNameResolver->resolveMaxKiloName(), IntegerType::class, [
                'label' => 'Max',
                'required' => false,
                'attr' => ['class' => 'advert_filter_kilo_max', 'placeholder' => 'ex: 40000'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le kilométrage maximum doit être un kilomètre valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le prix maximum ne peut pas être négatif',
                    ]),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                if (!empty($event->getData())) {
                    $data = [];
                    foreach ($event->getData() as $key => $item) {
                        $data[$key] = trim($item);
                    }
                    $event->setData($data);
                }
            });
    }
}

