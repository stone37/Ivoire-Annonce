<?php

namespace App\Handler;

use App\Entity\NewsletterData;
use App\Entity\User;
use App\Repository\NewsletterDataRepository;

class NewsletterSubscriptionHandler
{
    public function __construct(private NewsletterDataRepository $repository)
    {
    }

    public function subscribe(string $email): bool
    {
        $newsletterData = $this->repository->findOneBy(['email' => $email]);

        if ($newsletterData instanceof NewsletterData) {
            return false;
        }

        $user = $this->repository->findOneBy(['email' => $email]);

        if ($user instanceof User) {
            $this->updateUser($user);
        }

        $this->createNewsletter($email);

        return true;
    }

    public function unsubscribe(NewsletterData $data): void
    {
        $user = $this->repository->findOneBy(['email' => $data->getEmail()]);

        if ($user instanceof User) {
            $this->updateUser($user, false);
        }

        $this->deleteNewsletter($data);
    }

    private function createNewsletter($email): void
    {
        $newsletter = (new NewsletterData())->setEmail($email);

        $this->repository->add($newsletter, true);
    }

    private function deleteNewsletter(NewsletterData $data): void
    {
        $this->repository->remove($data, true);
    }


    private function updateUser(User $user, $subscribedToNewsletter = true): void
    {
        $user->setSubscribedToNewsletter($subscribedToNewsletter);
        $this->repository->flush();
    }
}

