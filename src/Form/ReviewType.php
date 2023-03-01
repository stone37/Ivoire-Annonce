<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Votre prénom'])
            ->add('email', EmailType::class, ['label' => 'Votre email'])
            ->add('rating', IntegerType::class, [
                'label' => 'Note',
                'attr' => ['min' => 0, 'max' => 10, 'placeholder' => 'Donner une note allant de 0 à 10.'],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => ['class' => 'md-textarea form-control', 'rows' => 4],
            ])
            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
                'attr' => ['class' => 'app-recaptchaToken']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Review::class
        ]);
    }
}
