<?php

namespace App\Util;

use App\Entity\Advert;
use App\Entity\Product;
use DateTime;

class OptionUtil
{
    public function getData(int $option): array
    {
        if ($option === Product::CATEGORY_ADVERT_HEAD_OPTION) {
            return [
                'name' => 'Annonce en tête de liste',
                'description' => 'Replace votre annonce en tête de liste et gardez une longueur d’avance sur les autres vendeurs.'
            ];
        } elseif ($option === Product::CATEGORY_ADVERT_URGENT_OPTION) {
            return [
                'name' => 'Logo urgent',
                'description' => 'Faites connaître votre intention de vendre rapidement.'
            ];
        } elseif ($option === Product::CATEGORY_ADVERT_HOME_GALLERY_OPTION) {
            return [
                'name' => 'Galerie de la page d\'accueil',
                'description' => 'Profitez en moyenne de plusieurs impressions par semaine grâce à un positionnement de choix sur notre page d\'accueil.'
            ];
        } else {
            return [
                'name' => 'Annonce en vedette',
                'url' => 'images/option/4.png',
                'description' => 'Les annonces avec l\'option <span class="font-weight-stone-600 text-default">en vedette</span> attirent 5 fois plus de visiteurs.'
            ];
        }
    }

    public function verify(Advert $advert, int $type): bool
    {
        $now = new DateTime();

        if ($type === Product::CATEGORY_ADVERT_HEAD_OPTION) {

            if (!$advert->getOptionAdvertHeadEndAt()) {
                return false;
            }

            return $advert->getOptionAdvertHeadEndAt() >= $now;
        } elseif ($type === Product::CATEGORY_ADVERT_URGENT_OPTION) {

            if (!$advert->getOptionAdvertUrgentEndAt()) {
                return false;
            }

            return $advert->getOptionAdvertUrgentEndAt() >= $now;
        } elseif ($type === Product::CATEGORY_ADVERT_HOME_GALLERY_OPTION) {

            if (!$advert->getOptionAdvertHomeGalleryEndAt()) {
                return false;
            }

            return $advert->getOptionAdvertHomeGalleryEndAt() >= $now;
        } elseif ($type === Product::CATEGORY_ADVERT_FEATURED_OPTION) {

            if (!$advert->getOptionAdvertFeaturedEndAt()) {
                return false;
            }

            return $advert->getOptionAdvertFeaturedEndAt() >= $now;
        }

        return false;
    }

    public function getName(Product $product): string
    {
        $text = '';

        if ($product->getCategory() === Product::CATEGORY_CREDIT) {
            $text = 'Achat de ' . $product->getPrice() . ' crédits';

            if ($product->getAmount()) {
                $text .= ' + ' . $product->getAmount() . ' crédits offerts';
            }

        } elseif ($product->getCategory() === Product::CATEGORY_ADVERT_OPTION) {
            if ($product->getOptions() === Product::CATEGORY_ADVERT_HEAD_OPTION) {
                $text = 'Annonce en tête de liste pendant ' . $product->getDays() . ' jour(s)';
            } elseif ($product->getOptions() === Product::CATEGORY_ADVERT_URGENT_OPTION) {
                $text = 'Logo urgent pendant ' . $product->getDays() . ' jour(s)';
            } elseif ($product->getOptions() === Product::CATEGORY_ADVERT_HOME_GALLERY_OPTION) {
                $text = 'Galerie de la page d\'accueil pendant ' . $product->getDays() . ' jour(s)';
            } elseif ($product->getOptions() === Product::CATEGORY_ADVERT_FEATURED_OPTION) {
                $text = 'Annonce en vedette pendant ' . $product->getDays() . ' jour(s)';
            }
        } elseif ($product->getCategory() === Product::CATEGORY_PREMIUM_PACK) {
            $text = 'Abonnement premium pendant ' . $product->getDays() . ' jour(s)';
        } else {
            $text = '';
        }

        return $text;
    }
}
