<?php

namespace App\Form\Filter;

use App\PropertyNameResolver\YearNameResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AutoYearFilterType extends AbstractFilterType
{
    public function __construct(private YearNameResolver $yearNameResolver)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add($this->yearNameResolver->resolveMinYearName(), ChoiceType::class, [
                'choices' => [
                    'Avant 2000' => 'Avant 2000', '2000' => '2000', '2001' => '2001', '2002' => '2002',
                    '2003' => '2003', '2004' => '2004', '2005' => '2005', '2006' => '2006', '2007' => '2007',
                    '2008' => '2008', '2009' => '2009', '2010' => '2010', '2011'  => '2011', '2012' => '2012',
                    '2013'  => '2013', '2014'  => '2014', '2015'  => '2015', '2016'  => '2016', '2017' => '2017',
                    '2018'  => '2018', '2019'  => '2019', '2020'  => '2020', '2021'  => '2021', '2022'  => '2022',
                    '2023'  => '2023'
                ],
                'label' => 'Min',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Min',
                'required' => false
            ])
            ->add($this->yearNameResolver->resolveMaxYearName(), ChoiceType::class, [
                'choices' => [
                    'Avant 2000' => 'Avant 2000', '2000' => '2000', '2001' => '2001', '2002' => '2002',
                    '2003' => '2003', '2004' => '2004', '2005' => '2005', '2006' => '2006', '2007' => '2007',
                    '2008' => '2008', '2009' => '2009', '2010' => '2010', '2011'  => '2011', '2012' => '2012',
                    '2013'  => '2013', '2014'  => '2014', '2015'  => '2015', '2016'  => '2016', '2017' => '2017',
                    '2018'  => '2018', '2019'  => '2019', '2020'  => '2020', '2021'  => '2021', '2022'  => '2022',
                    '2023'  => '2023'
                ],
                'label' => 'Max',
                'attr' => ['class' => 'mdb-select md-outline md-form dropdown-primary'],
                'placeholder' => 'Max',
                'required' => false
            ]);
    }
}

