<?php

namespace App\Form\Autos;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoMotoBrandChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'choices' => [
                'American ironhorse' => 'American ironhorse', 'AJP' => 'AJP', 'Aprilia' => 'Aprilia',
                'Apollo' => 'Apollo', 'AXR' => 'AXR', 'Barossa' => 'Barossa', 'Benelli' => 'Benelli',
                'Beta' => 'Beta', 'Big Dog' => 'Big Dog', 'Bimota' => 'Bimota', 'BMW' => 'BMW', 'Boxe' => 'Boxe',
                'Boss Hoss' => 'Boss Hoss', 'Bourget' => 'Bourget', 'BSA' => 'BSA', 'Buell' => 'Buell',
                'Bullit' => 'Bullit', 'Bultaco' => 'Bultaco', 'Can Am' => 'Can Am', 'CCM' => 'CCM',
                'Ciello' => 'Ciello', 'Ch Racing' => 'Ch Racing', 'Cushman' => 'Cushman', 'Clipic' => 'Clipic',
                'Conti motir' => 'Conti motor', 'Desperado' => 'Desperado', 'Daelim' => 'Daelim',
                'Dakota' => 'Dakota', 'Derbi' => 'Derbi', 'Dirt bike' => 'Dirt bike', 'Ducati' => 'Ducati',
                'Greeves' => 'Greeves', 'Harley-Davidson' => 'Harley-Davidson', 'Hodaka' => 'Hodaka',
                'Honda' => 'Honda', 'Husqvarna' => 'Husqvarna', 'Hyosung' => 'Hyosung', 'Indian' => 'Indian',
                'Jawa' => 'Jawa', 'JCM' => 'JCM', 'Jialing' => 'Jialing', 'Jincheng' => 'Jincheng',
                'Jinlun' => 'Jinlun', 'JM Motor' => 'JM Motor', 'Jotagas' => 'Jotagas', 'Kawasaki' => 'Kawasaki',
                'KTM' => 'KTM', 'Kymco' => 'Kymco', 'Lifan' => 'Lifan', 'Moto Guzzi' => 'Moto Guzzi', 'MV Agusta' => 'MV Agusta',
                'Piaggio' => 'Piaggio', 'Polaris' => 'Polaris', 'Praga' => 'Praga', 'PZF' => 'PZF', 'Rato' => 'Rato',
                'Razzo' => 'Razzo', 'Revatto' => 'Revatto', 'Saxon' => 'saxon', 'Scorpa' => 'Scorpa',
                'Sea Doo' => 'Sea Doo', 'Sherco' => 'Sherco', 'Shineray' => 'Shineray', 'Ski Team' => 'Ski Team',
                'SMC' => 'SMC', 'Spigaou' => 'Spigaou', 'Suzuki' => 'Suzuki', 'SYM' => 'SYM', 'TM' => 'TM',
                'Titan' => 'Titan', 'Triumph' => 'Triumph', 'Ural' => 'Ural', 'Vento' => 'Vento',
                'Victory' => 'Victory', 'Vincent' => 'Vincent', 'Von Dutch' => 'Von Dutch', 'Yamaha' => 'Yamaha',
                'Yamasaki' => 'Yamasaki', 'Autre' => 'Autre'
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
        return 'app_auto_moto_brand_choice';
    }
}
