<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activeThread', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module de messagerie',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activeAdFavorite', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module des annonces favoris',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activeAlert', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module d\'alerte des annonces',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activeAdvertSimilar', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module des annonces similaire',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activeCredit', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module de paiement par crédit',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activeCardPayment', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module de paiement par carte bancaire et mobile money',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activePack', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé le module des comptes premium',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ]) // Fini
            ->add('activeParrainage', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activé',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('activeRegisterDrift', ChoiceType::class, [
                'choices' => ['Oui' => true, 'Non' => false],
                'label' => 'Activée',
                'expanded' => true,
                'multiple' => false,
                'required' => false
            ])
            ->add('numberAdvertPerPage', IntegerType::class, [
                'label' => 'Nombre d\'annonce par page',
                'required' => false
            ])
            ->add('numberUserAdvertPerPage', IntegerType::class, [
                'label' => 'Nombre d\'annonce par page - dashboard',
                'required' => false
            ])
            ->add('numberUserAdvertFavoritePerPage', IntegerType::class, [
                'label' => 'Nombre favoris par page - dashboard',
                'required' => false
            ])
            ->add('parrainCreditOffer', IntegerType::class, [
                'label' => 'Crédit offert au parrain par parrainage',
                'required' => false
            ])
            ->add('fioleCreditOffer', IntegerType::class, [
                'label' => 'Crédit offert au fiole par parrainage',
                'required' => false
            ])
            ->add('parrainageNumberAdvertRequired', IntegerType::class, [
                'label' => 'Nombre d\'annonces',
                'help' => 'Nombre d\'annonces à crée par le fiole pour activé la recompense.',
                'required' => false
            ])
            ->add('registerDriftCreditOffer', IntegerType::class, [
                'label' => 'Crédit offert par création de compte',
                'required' => false
            ])
            ->add('registerDriftNumberAdvertRequired', IntegerType::class, [
                'label' => 'Nombre d\'annonces',
                'help' => 'Nombre d\'annonces requis pour activé la recompense',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
