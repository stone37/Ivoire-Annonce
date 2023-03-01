<?php

namespace App\Form\Immobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImmobilierYearChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'Avant 1990' => 'Avant 1990', '1990' => '1990', '1991' => '1991', '1992' => '1992',
                '1993' => '1993', '1994' => '1994', '1995' => '1995', '1996' => '1996', '1997' => '1997', '1998' => '1998',
                '1999' => '1999', '2000' => '2000', '2001' => '2001', '2002' => '2002', '2003' => '2003',
                '2004' => '2004', '2005' => '2005', '2006' => '2006', '2007' => '2007', '2008' => '2008',
                '2009' => '2009', '2010' => '2010', '2011'  => '2011', '2012' => '2012', '2013'  => '2013',
                '2014'  => '2014', '2015'  => '2015', '2016'  => '2016', '2017' => '2017', '2018'  => '2018',
                '2019'  => '2019', '2020'  => '2020', '2021'  => '2021', '2022'  => '2022', '2023'  => '2023'
            ],
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'app_immobilier_year_choice';
    }
}
