<?php

namespace App\Manager;

use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Settings;
use App\Entity\User;
use App\Model\AdvertSearch;
use App\Repository\CategoryRepository;
use App\Service\UniqueNumberGenerator;
use App\Util\AdvertUtil;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RequestStack;


class AdvertManager
{
    private ?Settings $settings;

    public function __construct(
        private RequestStack $request,
        private CategoryRepository $categoryRepository,
        private UniqueNumberGenerator $generator,
        private AdvertUtil $util,
        SettingsManager $settingsManager
    )
    {
        $this->settings = $settingsManager->get();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function createAdvert(User $user): Advert
    {
        return (new Advert())
            ->setCategory($this->getCategory())
            ->setSubCategory($this->getSubCategory())
            ->setOwner($user)
            ->setReference($this->generator->generate(8));
    }

    public function createForm(): string
    {
        return $this->util->createForm($this->getCategorySlug(), $this->getSubCategorySlug());
    }

    public function editForm(Advert $advert): string
    {
        return $this->util->createForm($advert->getCategory()->getSlug(), $advert->getSubCategory()->getSlug());
    }

    public function viewRoute(): string
    {
        return $this->util->viewRoute($this->getCategorySlug(), $this->getSubCategorySlug());
    }

    public function viewEditRoute(Advert $advert): string
    {
        return $this->util->viewRoute($advert->getCategory()->getSlug(), $advert->getSubCategory()->getSlug());
    }

    public function showViewRoute(): string
    {
        return $this->util->showViewRoute($this->getCategorySlug(), $this->getSubCategorySlug());
    }

    public function createFilterForm(): string
    {
        return $this->util->createFilterForm($this->getCategorySlug());
    }

    public function filterEntity()
    {
        return (new ($this->util->createFilterEntity($this->getCategorySlug())))
                ->setCategory($this->request->getCurrentRequest()->attributes->get('category_slug'))
                ->setSubCategory($this->request->getCurrentRequest()->attributes->get('sub_category_slug'));
    }

    public function showFilterViewRoute(): string
    {
        return $this->util->showFilterViewRoute($this->getCategorySlug());
    }

    public function activatedDays(): DateTime
    {
        return (new DateTime())->modify("-{$this->settings->getAdvertActivatedDays()} days");
    }

    public function validate(Advert $advert)
    {
        $advert->setValidatedAt(new DateTime());
        $advert->setDeniedAt(null);
        $advert->setDeletedAt(null);
    }

    public function denied(Advert $advert)
    {
        $advert->setDeniedAt(new DateTime());
        $advert->setValidatedAt(null);
        $advert->setDeletedAt(null);
    }

    public function delete(Advert $advert)
    {
        $advert->setDeletedAt(new DateTime());
    }

    public function getMainCategory(): ?Category
    {
        if ($this->request->getCurrentRequest()->attributes->has('sub_category_slug')) {
            return $this->categoryRepository->getEnabledBySlug($this->request->getCurrentRequest()->attributes->get('sub_category_slug'));
        }

        return $this->categoryRepository->getEnabledBySlug($this->request->getCurrentRequest()->attributes->get('category_slug'));
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getCategory(): ?Category
    {
        return $this->categoryRepository->getEnabledBySlug($this->getCategorySlug());
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getSubCategory(): ?Category
    {
        return $this->categoryRepository->getEnabledBySlug($this->getSubCategorySlug());
    }

    private function getCategorySlug(): string
    {
        return (string) $this->request->getCurrentRequest()->attributes->get('category_slug');
    }

    private function getSubCategorySlug(): string
    {
        return (string) $this->request->getCurrentRequest()->query->get('c');
    }
}
