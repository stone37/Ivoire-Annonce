<?php

namespace App\Form\Filter;

use App\PropertyNameResolver\PriceNameResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;

class PriceFilterType extends AbstractFilterType
{
    public function __construct(
        private PriceNameResolver $priceNameResolver
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add($this->priceNameResolver->resolveMinPriceName(), IntegerType::class, [
                'label' => 'Min',
                'required' => false,
                'attr' => ['class' => 'advert_filter_price_min', 'placeholder' => 'De'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le prix minimum doit être un prix valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le prix minimum ne peut pas être négatif',
                    ]),
                ],
            ])
            ->add($this->priceNameResolver->resolveMaxPriceName(), IntegerType::class, [
                'label' => 'Max',
                'required' => false,
                'attr' => ['class' => 'advert_filter_price_max', 'placeholder' => 'à'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'Le prix maximum doit être un prix valide',
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

