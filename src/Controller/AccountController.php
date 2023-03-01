<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Dto\ProfileUpdateDto;
use App\Entity\User;
use App\Exception\TooManyEmailChangeException;
use App\Form\UpdateAvatarForm;
use App\Repository\UserRepository;
use App\Service\ProfileService;
use App\Form\UpdatePasswordForm;
use App\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class AccountController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $repository,
        private ProfileService $profileService,
        private EntityManagerInterface $em
    )
    {

    }

    #[Route(path: '/profil/{id}', name: 'app_user_profil_index', requirements: ['id' => '\d+'])]
    public function index(Request $request, User $user)
    {
        /*$search = (new Search())->setUser($user);
        $search = $this->hydrate($request, $search);

        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        $number = $em->getRepository(Advert::class)->getUserAdvertActiveNumber($search);
        $adverts = $em->getRepository(Advert::class)->getUserAdvertActive($search);

        $adverts = $paginator->paginate($adverts, $request->query->getInt('page', 1), 9);

        return $this->render('user/profil/profil.html.twig', [
            'user' => $user,
            'settings' => $this->settings,
            'number' => $number,
            'adverts' => $adverts,
        ]);*/

    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/edit', name: 'app_user_profil_edit')]
    public function edit(Request $request): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUserOrThrow();

        $formUpdate = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        $formAvatarUpdate = $this->createForm(UpdateAvatarForm::class, $user, ['action' => $this->generateUrl('app_user_avatar')]);

        $formUpdate->handleRequest($request);

        try {
            if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
                $data = $formUpdate->getData();
                $this->profileService->updateProfile($data);
                $this->repository->flush();

                if ($user->getEmail() !== $data->email) {
                    $this->addFlash(
                        'success',
                        "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement"
                    );
                } else {
                    $this->addFlash('success', 'Votre profil a bien été mis à jour');
                }

                return $this->redirectToRoute('app_user_profil_edit');
            }
        } catch (TooManyEmailChangeException) {
            $this->addFlash('error', "Vous avez déjà un changement d'email en cours.");
        }

        return $this->render('user/account/edit.html.twig', [
            'form_update' => $formUpdate->createView(),
            'form_avatar' => $formAvatarUpdate->createView(),
            'user' => $user
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/avatar', name: 'app_user_avatar')]
    public function avatar(Request $request): RedirectResponse
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUserOrThrow();

        $form = $this->createForm(UpdateAvatarForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Avatar mis à jour avec succès');
        }

        return $this->redirectToRoute('app_user_profil_edit');
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/profil/modifier-mot-passe', name: 'app_user_change_password')]
    public function changePassword(Request $request): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUserOrThrow();
        $form = $this->createForm(UpdatePasswordForm::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            $this->repository->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('app_user_change_password');
        }

        return $this->render('user/account/change_password.html.twig', [
            'form_password' => $form->createView(),
            'user' => $user
        ]);
    }
}
