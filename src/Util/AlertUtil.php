<?php

namespace App\Util;

use App\Manager\AlertManager;
use App\Repository\CategoryRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Security;

class AlertUtil
{
    public function __construct(
        private CategoryRepository $repository,
        private Security $security,
        private AlertManager $manager
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function verify(string $category_slug, ?string $sub_category_slug): bool
    {
        if ($this->security->getUser() === null) {
            return false;
        }

        $category = $this->repository->getEnabledBySlug($category_slug);
        $subCategory = null;

        if ($sub_category_slug) {
            $subCategory = $this->repository->getEnabledBySlug($sub_category_slug);
        }

        //dump($category_slug, $sub_category_slug);

        return $this->manager->hasAlert($category, $subCategory);
    }
}