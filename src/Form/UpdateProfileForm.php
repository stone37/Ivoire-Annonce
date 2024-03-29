<?php

namespace App\Form;

use App\Dto\ProfileUpdateDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @method ProfileUpdateDto getData()
 */
class UpdateProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email', EmailType::class, ['label' => 'Adresse e-mail'])
            ->add('username', TextType::class, ['required' => false, 'label' => "Nom d'utilisateur (facultatif)"])
            ->add('firstName', TextType::class, ['label' => 'Prénom'])
            ->add('lastName', TextType::class, ['label' => 'Nom'])
            ->add('phone', TextType::class, ['label' => 'Téléphone'])
            ->add('address', TextType::class, ['label' => 'Adresse (facultatif)', 'required' => false])
            ->add('phoneState', CheckboxType::class, ['label' => 'Numéro whatsapp', 'required' => false])
            ->add('country', CountryType::class, [
                'label' => 'Pays (facultatif)',
                'attr'  => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Pays (facultatif)',
                'data' => 'CI',
                'required' => false,
            ])
            ->add('city', TextType::class, ['label' => 'Ville (facultatif)', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProfileUpdateDto::class,
        ]);
    }
}
