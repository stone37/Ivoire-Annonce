<?php

namespace App\Validation;

use App\Entity\Advert;
use Symfony\Component\Form\FormInterface;

class AdvertValidationGroupResolver
{
    public function __invoke(FormInterface $form): array
    {
        $groups = ['Default'];

        /** @var Advert $advert */
        $advert = $form->getData();

        if ($advert->getCategory()->getSlug() === ValidateGroupData::AUTO_MOTO)
        {
            if ($advert->getSubCategory()->getSlug() === ValidateGroupData::OCCASION)
            {
                $groups = ['Default', 'VOITURE_OCCASION'];
            }
            elseif($advert->getSubCategory()->getSlug() === ValidateGroupData::LOCATION)
            {
                $groups = ['Default', 'LOCATION_VOITURE'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::MOTOCROSS)
            {
                $groups = ['Default', 'MOTOCROSS'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::MOTO_SPORT)
            {
                $groups = ['Default', 'MOTO_SPORT'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::ROUTIERE)
            {
                $groups = ['Default', 'ROUTIERE'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::JETSKI)
            {
                $groups = ['Default', 'JETSKI'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::BATEAUX)
            {
                $groups = ['Default', 'VEDETTE_BATEAU'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::CAMION)
            {
                $groups = ['Default', 'CAMION'];
            }
        }
        elseif($advert->getCategory()->getSlug() === ValidateGroupData::IMMOBILIER)
        {
            if ($advert->getSubDivision()->getSlug() === ValidateGroupData::APPARTEMENT_VENTE || $advert->getSubDivision()->getSlug() === ValidateGroupData::APPARTEMENT_LOCATION)
            {
                $groups = ['Default', 'APPARTEMENT'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::MAISON_VENTE || $advert->getSubDivision()->getSlug() === ValidateGroupData::MAISON_LOCATION)
            {
                $groups = ['Default', 'MAISON'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::VILLA_VENTE || $advert->getSubDivision()->getSlug() === ValidateGroupData::VILLA_LOCATION)
            {
                $groups = ['Default', 'VILLA'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::CHAMBRE)
            {
                $groups = ['Default', 'CHAMBRE'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::DUPLEX_VENTE || $advert->getSubDivision()->getSlug() === ValidateGroupData::DUPLEX_LOCATION)
            {
                $groups = ['Default', 'DUPLEX'];
            } elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::TERRAIN_VENTE || $advert->getSubDivision()->getSlug() === ValidateGroupData::TERRAIN_LOCATION)
            {
                $groups = ['Default', 'TERRAIN'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::COLOCATION)
            {
                $groups = ['Default', 'COLOCATION'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::STUDIO)
            {
                $groups = ['Default', 'STUDIO'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::STUDIO_AMERICAIN)
            {
                $groups = ['Default', 'STUDIO_AMERICAIN'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::PARKING)
            {
                $groups = ['Default', 'PARKING'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::BUREAU)
            {
                $groups = ['Default', 'ESPACE_COMMERCIAUX'];
            }
        }
        elseif ($advert->getCategory()->getSlug() === ValidateGroupData::ACHAT_VENTE)
        {
            $groups = ['Default', 'ACHAT_VENTE'];

            if ($advert->getSubDivision()->getSlug() === ValidateGroupData::TABLETTE)
            {
                $groups = ['Default', 'ACHAT_VENTE', 'IPAD_TABLETTE'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::ORDINATEUR)
            {
                $groups = ['Default', 'ACHAT_VENTE', 'ORDINATEUR_BUREAU'];
            }
            elseif ($advert->getSubDivision()->getSlug() === ValidateGroupData::PORTABLE)
            {
                $groups = ['Default', 'ACHAT_VENTE', 'ORDINATEUR_PORTABLE'];
            }
        }

        return $groups;
    }
}
