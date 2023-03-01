<?php

namespace App\Util;

use App\Entity\Advert;
use App\Repository\FavoriteRepository;
use Symfony\Component\Security\Core\Security;

class FavoriteUtil
{
    public function __construct(
        private FavoriteRepository $repository,
        private Security $security
    )
    {
    }

    public function verify(Advert $advert): bool
    {
        if ($this->security->getUser() === null) {
            return false;
        }

        $favorite = $this->repository->findOneBy(['advert' => $advert, 'owner' => $this->security->getUser()]);

        return (bool) $favorite;
    }
}

