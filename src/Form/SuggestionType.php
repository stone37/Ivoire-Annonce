<?php

namespace App\Form;

use App\Entity\Suggestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuggestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Votre prénom'])
            ->add('email', EmailType::class, ['label' => 'Votre email'])
            ->add('subject', IntegerType::class, ['label' => 'Sujet de votre message'])
            ->add('comment', TextareaType::class, [
                'label' => 'Votre message',
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
            'data_class' => Suggestion::class
        ]);
    }
}
