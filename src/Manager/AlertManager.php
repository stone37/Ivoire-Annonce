<?php

namespace App\Manager;

use App\Entity\Alert;
use App\Entity\Category;
use App\Repository\AlertRepository;
use Symfony\Component\Security\Core\Security;

class AlertManager
{
    public function __construct(
        private Security $security,
        private AlertRepository $repository
    )
    {
    }

    public function createAlert(Category $category, Category $subCategory = null): Alert
    {
        return (new Alert())
            ->setCategory($category)
            ->setSubCategory($subCategory)
            ->setOwner($this->security->getUser());
    }

    public function hasAlert(Category $category, Category $subCategory = null): bool
    {
        $alert = $this->repository->findOneBy(['category' => $category, 'subCategory' => $subCategory, 'owner' => $this->security->getUser()]);

        return (bool) $alert;
    }
}
