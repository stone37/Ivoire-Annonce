<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('phone', TextType::class, ['label' => 'Téléphone']);

        /** @var ?User $user */
        $user = $builder->getData();

        if ($user && empty($user->getEmail())) {
            $builder->add('email', EmailType::class, ['label' => 'Adresse e-mail']);
        }

        if ($user && empty($user->getFirstname())) {
            $builder->add('firstname', TextType::class, ['label' => 'Prénom']);
        }

        if ($user && empty($user->getLastname())) {
            $builder->add('lastname', TextType::class, ['label' => 'Nom']);
        }

        if ($user && !$user->useOauth()) {
            $passwordAttrs = ['minlength' => 8, 'maxlength' => 4096];
            $builder
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
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}
