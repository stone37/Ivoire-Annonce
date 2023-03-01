<?php

namespace App\Form;

use App\Data\AdvertSearchRequestData;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdvertSearchRequestDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Category $category */
        $category = $options['category'];

        $builder
            ->add('data', TextType::class, [
                'attr' => [
                    'class' => 'form-control advert_search_data',
                    'placeholder' => ($category) ? 'Recherche dans ' . strtolower($category->getName()) : 'Que cherchez-vous ?'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdvertSearchRequestData::class
        ])->setRequired(['category']);
    }
}


