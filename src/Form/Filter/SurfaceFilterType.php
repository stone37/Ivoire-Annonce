<?php

namespace App\Form\Filter;

use App\PropertyNameResolver\SurfaceNameResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;

class SurfaceFilterType extends AbstractFilterType
{
    public function __construct(
        private SurfaceNameResolver $surfaceNameResolver
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add($this->surfaceNameResolver->resolveMinSurfaceName(), IntegerType::class, [
                'label' => 'Min',
                'required' => false,
                'attr' => ['class' => 'advert_filter_surface_min', 'placeholder' => 'De'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'La surface minimum doit être une surface valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'La surface minimum ne peut pas être négatif',
                    ]),
                ],
            ])
            ->add($this->surfaceNameResolver->resolveMaxSurfaceName(), IntegerType::class, [
                'label' => 'Max',
                'required' => false,
                'attr' => ['class' => 'advert_filter_surface_max', 'placeholder' => 'à'],
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                        'message' => 'La surface maximum doit être une surface valide',
                    ]),
                    new PositiveOrZero([
                        'message' => 'La surface maximum ne peut pas être négatif',
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

