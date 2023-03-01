<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Invitation;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\InvitationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class ParrainageController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(private InvitationRepository $repository, SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/parrainage', name: 'app_parrainage_index')]
    public function index(): NotFoundHttpException|Response
    {
        if (!$this->settings->isActiveParrainage()) {
            return $this->createNotFoundException('Bad request');
        }

        $user = $this->getUserOrThrow();
        $invitation = $this->repository->findByUser($user);

        if (null === $invitation) {
            $invitation = (new Invitation())->setOwner($user);

            $this->repository->add($invitation, true);
        }

        return $this->render('user/invitation/index.html.twig', [
            'user' => $user,
            'invitation' => $invitation
        ]);
    }
}
