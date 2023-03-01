<?php

namespace App\Form;

use App\Entity\CategoryPremium;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryPremiumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('enabled', CheckboxType::class, ['label' => 'Activée', 'required' => false,])
            ->add('categories',  CategoryHasParentChoiceType::class, [
                'label' => 'Catégories',
                'attr' => [
                    'class' => 'mdb-select md-form md-outline dropdown-primary',
                    'data-label-select-all' => 'Tout sélectionnée'
                ],
                'label_attr' => ['class' => 'ml-1'],
                'placeholder' => 'Catégories',
                'multiple' => true,
                'required' => true
            ])
           /* ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => function (CategoryRepository $er) {
                    return $er->getCategoriesWithParentNotNull();
                },
                'label' => 'Catégories',
                'multiple' => true,
                'required' => true,
                'placeholder' => 'Catégories',
                'attr' => [
                    'class' => 'mdb-select md-form md-outline dropdown-stone',
                    'data-label-select-all' => 'Tout selectionnée'
                ],
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategoryPremium::class,
        ]);
    }
}