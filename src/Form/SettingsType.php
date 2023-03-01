<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, ['label' => 'Nom'])
            ->add('email',TextType::class, ['label' => 'E-mail'])
            ->add('phone',TextType::class, ['label' => 'Telephone'])
            ->add('fax',TextType::class, ['label' => 'Fax (facultatif)', 'required' => false])
            ->add('address',TextType::class, ['label' => 'Adresse (facultatif)', 'required' => false])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultatif)',
                'attr'  => ['class' => 'form-control md-textarea', 'rows'  => 5],
                'required' => false
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'attr'  => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Pays (facultatif)',
                'data' => 'CI',
                'required' => false
            ])
            ->add('city', TextType::class, ['label' => 'Ville (facultatif)', 'required' => false])
            ->add('facebookAddress', TextType::class, ['label' => 'Adresse Facebook (facultatif)', 'required' => false])
            ->add('twitterAddress', TextType::class, ['label' => 'Adresse Twitter (facultatif)', 'required' => false])
            ->add('linkedinAddress', TextType::class, ['label' => 'Adresse Linkedin (facultatif)', 'required' => false])
            ->add('instagramAddress', TextType::class, ['label' => 'Adresse Instagram (facultatif)', 'required' => false])
            ->add('youtubeAddress', TextType::class, ['label' => 'Adresse Youtube (facultatif)', 'required' => false])
            ->add('file', VichFileType::class, ['label' => 'Logo du site (facultatif)', 'required' => false])
            ->add('baseCurrency', CurrencyChoiceType::class, [
                'label' => 'Devise de référence',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Devise de référence'
            ])
            ->add('defaultLocale', LocaleChoiceType::class, [
                'label' => 'Paramètres régionaux par défaut',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Paramètres régionaux par défaut'
            ])
            ->add('advertActivatedDays', IntegerType::class, [
                'label' => 'Jour(s) de validiter des annonces',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
