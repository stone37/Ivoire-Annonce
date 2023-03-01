<?php

namespace App\Util;

use App\Entity\Advert;
use App\Entity\AdvertPicture;
use Doctrine\ORM\PersistentCollection;

class AdvertUtil
{
    public function getTypeData(int $type): string
    {
        return Advert::TYPE_DATA[$type-1];
    }

    public function getProcessingData(int $processing): string
    {
        return Advert::PROCESSING_DATA[$processing-1];
    }

    public function getImagePrincipale(PersistentCollection $pictures): ?AdvertPicture
    {
        if (!$pictures->count()) {
            return null;
        }

        $data = null;

        /** @var AdvertPicture $picture */
        foreach ($pictures as $picture) {
            if ($picture->isPrincipale()) {
                $data = $picture;
            }
        }

        if (!$data) {
            $data = $pictures->first();
        }

        return $data;
    }

    public function createForm(string $category_slug, string $sub_category_slug): string
    {
        $prefix = $this->prefix($category_slug, $sub_category_slug);

        return 'App\Form\\' . ucfirst($this->folder($category_slug)) . '\\' . $prefix . 'Type';
    }

    public function editForm(string $category_slug, string $sub_category_slug): string
    {
        $prefix = $this->prefix($category_slug, $sub_category_slug);

        return 'App\Form\\' . ucfirst($this->folder($category_slug)) . '\\' . $prefix . 'EditType';
    }

    public function viewRoute(string $category_slug, string $sub_category_slug): string
    {
        $prefix = $this->prefix($category_slug, $sub_category_slug);

        return $this->folder($category_slug) . '/' . strtolower($prefix) .'.html.twig';
    }

    public function showViewRoute(string $category_slug, string $sub_category_slug): string
    {
        $data = '';

        if ($sub_category_slug) {
            foreach (explode('-', $sub_category_slug) as $item) {
                $data .= ucfirst($item);
            }
        } else {
            foreach (explode('-', $category_slug) as $item) {
                $data .= ucfirst($item);
            }
        }

        return $this->folder($category_slug) . '/' . $data . '.html.twig';
    }

    public function createFilterForm(string $category_slug): string
    {
        return 'App\Form\Filter\\' . ucfirst($this->folder($category_slug)) . 'FilterType';
    }

    public function createFilterEntity(string $category_slug)
    {
        return 'App\Model\\' . ucfirst($this->folder($category_slug)) . 'Search';
    }

    public function showFilterViewRoute(string $category_slug): string
    {
        return  $this->folder($category_slug) . 'Filter.html.twig';
    }

    private function folder(string $category_slug): string
    {
        return explode('-', $category_slug)[0];
    }

    private function prefix(string $category_slug, string $sub_category_slug): string
    {
        $data = '';

        if ($sub_category_slug) {
            foreach (explode('-', $sub_category_slug) as $item) {
                $data .= ucfirst($item);
            }
        } else {
            foreach (explode('-', $category_slug) as $item) {
                $data .= ucfirst($item);
            }
        }

        return $data;
    }
}
