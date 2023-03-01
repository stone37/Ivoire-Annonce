<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $passwordAttrs = ['minlength' => 8, 'maxlength' => 4096];

        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('roles', ChoiceType::class, [
                'choices' => ['Admin' => 'ROLE_ADMIN', 'Super Admin' => 'ROLE_SUPER_ADMIN'],
                'label' => 'Rôles',
                'multiple' => true,
                'required' => true,
                'label_attr' => ['class' => 'ml-1'],
                'placeholder' => 'Rôles',
                'attr' => [
                    'class' => 'mdb-select md-form md-outline dropdown-primary',
                    'data-label-select-all' => 'Tout sélectionnée'
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                    ]),
                ],
                'first_options' => ['label' => 'Mot de passe', 'attr' => $passwordAttrs],
                'second_options' => ['label' => 'Confirmer le mot de passe', 'attr' => $passwordAttrs],
            ])
            ->add('isVerified', CheckboxType::class, ['label' => 'Activée']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}
