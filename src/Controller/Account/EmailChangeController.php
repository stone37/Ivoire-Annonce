<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\UserRepository;
use App\Entity\EmailVerification;
use App\Service\ProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class EmailChangeController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private ProfileService $service,
        private EntityManagerInterface $em,
        private UserRepository $userRepository
    )
    {;
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/email-confirm/{token}', name: 'app_user_email_confirm')]
    public function confirm(EmailVerification $emailVerification): Response
    {
        if ($emailVerification->isExpired()) {
            $this->addFlash('error', 'Cette demande de confirmation a expiré');
        } else {
            $user = $this->userRepository->findOneByEmail($emailVerification->getEmail());

            // Un utilisateur existe déjà avec cet email
            if ($user) {
                $this->addFlash('error', 'Cet email est déjà utilisé');

                return $this->redirectToRoute('app_login');
            }

            $this->service->updateEmail($emailVerification);
            $this->em->flush();
            $this->addFlash('success', 'Votre email a bien été modifié');
        }

        return $this->redirectToRoute('app_user_profil_edit');
    }
}
