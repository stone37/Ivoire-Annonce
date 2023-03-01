<?php

namespace App\Manager;


use App\Entity\Invitation;
use App\Entity\User;
use App\Repository\InvitationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserManager
{
    public function __construct(
        private InvitationRepository $invitationRepository,
        private  FlashBagInterface $bag,
        private UrlGeneratorInterface $generator
    )
    {
    }

    public function createUser(Request $request): User|RedirectResponse
    {
        $user = new User();

        if ($request->query->has('code')) {
            try {
                $invitation = $this->verifyCode($request->query->get('code'));
            } catch (Exception $e) {
                $this->bag->add('error', $e->getMessage());

                return new RedirectResponse($this->generator->generate('app_register'));
            }

            $user->setInvitation($invitation);
        }

        return $user;
    }

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function verifyCode(string $code): ?Invitation
    {
        $invitation = $this->invitationRepository->findByCode($code);

        if (!$invitation) {
            throw new Exception('Code d\'invitation erroné.');
        }

        return $invitation;
    }
}
