<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Advert;
use App\Entity\AdvertPicture;
use App\Entity\Settings;
use App\Event\AdvertBadEvent;
use App\Event\AdvertDeleteEvent;
use App\Event\AdvertEditEvent;
use App\Event\AdvertInitEvent;
use App\Event\AdvertPreDeleteEvent;
use App\Event\AdvertPreEditEvent;
use App\Manager\AdvertManager;
use App\Manager\SettingsManager;
use App\Repository\AdvertPictureRepository;
use App\Repository\AdvertRepository;
use App\Storage\AdvertStorage;
use App\Storage\CartStorage;
use App\Storage\CommandeStorage;
use JetBrains\PhpStorm\ArrayShape;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class AdvertController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(
        private AdvertRepository $repository,
        private AdvertPictureRepository $pictureRepository,
        private CommandeStorage $commandeStorage,
        private CartStorage $cartStorage,
        private AdvertStorage $advertStorage,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher,
        private AdvertManager $manager,
        SettingsManager $settingsManager,

    )
    {
        $this->settings = $settingsManager->get();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/adverts', name: 'app_user_advert_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $this->commandeStorage->remove();
        $this->cartStorage->init();
        $this->advertStorage->remove();

        $user = $this->getUserOrThrow();

        $qb = $this->repository->getByUser($user);
        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), $this->settings->getNumberUserAdvertPerPage());

        return $this->render('user/advert/index.html.twig', [
            'adverts' => $adverts
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/adverts/validated', name: 'app_user_advert_validated_index', methods: ['GET'])]
    public function validate(Request $request): Response
    {
        $this->commandeStorage->remove();
        $this->cartStorage->init();
        $this->advertStorage->remove();

        $user = $this->getUserOrThrow();

        $qb = $this->repository->getValidatedByUser($user);
        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), $this->settings->getNumberUserAdvertPerPage());

        return $this->render('user/advert/enabled.html.twig', [
            'adverts'  => $adverts
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/adverts/{category_slug}/{id}/edit', name: 'app_user_advert_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): RedirectResponse|Response
    {
        $this->dispatcher->dispatch(new AdvertInitEvent($request));

        $advert = $this->repository->getEnabledByById($id);
        $form = $this->createForm($this->manager->editForm($advert), $advert);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->dispatcher->dispatch(new AdvertPreEditEvent($advert, $request));

            $this->repository->flush();

            $this->dispatcher->dispatch(new AdvertEditEvent($advert));

            $redirect = $advert->getValidatedAt() ?
                $this->generateUrl('app_user_advert_validated_index') :
                $this->generateUrl('app_user_advert_index');

            return new RedirectResponse($redirect);
        } else {
            $this->dispatcher->dispatch(new AdvertBadEvent($advert, $request));
        }

        return $this->render('user/advert/edit.html.twig', [
            'form' => $form->createView(),
            'view' => $this->manager->viewEditRoute($advert),
            'advert' => $advert
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/adverts/images/{id}/delete', name: 'app_user_advert_image_delete', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['GET', 'POST'])]
    public function deleteImage(Request $request, AdvertPicture $picture): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        if ($picture->isPrincipale()) {
            $advert = $picture->getAdvert();
            $id = null;

            /** @var AdvertPicture $image */
            foreach ($advert->getImages() as $image) {
                if ($image->getId() !== $picture->getId()) {
                    $image->setPrincipale(true);
                    $id = $image->getId();
                    break;
                }
            }

            $this->pictureRepository->remove($picture, true);

            return new JsonResponse(['success' => true, 'id' => $id]);
        }

        $picture->setAdvert(null);

        $this->pictureRepository->remove($picture, true);

        return new JsonResponse(['success' => true, 'id' => null]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/adverts/images/{id}/change/{imageId}/principale', name: 'app_advert_image_change_principale', requirements: ['id' => '\d+', 'imageId' => '\d+'], options: ['expose' => true])]
    public function changePrincipale(Request $request, Advert $advert, int $imageId): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        /** @var AdvertPicture $image */
        foreach ($advert->getImages() as $image) {
            $image->setPrincipale(false);

            if ($image->getId() === $imageId) {
                $image->setPrincipale(true);
            }
        }

        $this->repository->flush();

        $data = $request->getSession()->get($this->provideKey());

        if (!$data) {
            return new JsonResponse(true);
        }

        $array = [];

        foreach ($data as $values) {
            foreach ($values as $key => $value) {
                $array[] = [$key => 0];
            }
        }

        $request->getSession()->set($this->provideKey(), $array);

        return new JsonResponse(true);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/adverts/{id}/delete', name: 'app_user_advert_delete', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['GET', 'POST'])]
    public function delete(Request $request, Advert $advert): NotFoundHttpException|RedirectResponse|JsonResponse|Response|null
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        $form = $this->deleteForm($advert);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $event = new AdvertPreDeleteEvent($advert);
                $this->dispatcher->dispatch($event);

                if (null !== $response = $event->getResponse()) {
                    return $response;
                }

                $this->manager->delete($advert);
                $this->repository->flush();

                $this->dispatcher->dispatch(new AdvertDeleteEvent($advert));

                if (null !== $response = $event->getResponse()) {
                    return $response;
                }

                $this->addFlash('success', 'L\'annonce a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'annonce n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette annonce ?';

        $render = $this->render('ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $advert,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Advert $advert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_user_advert_delete', ['id' => $advert->getId()]))
            ->getForm();
    }

    #[ArrayShape(['modal' => "\string[][]"])] private function configuration(): array
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ]
            ]
        ];
    }

    private function provideKey(): string
    {
        return '_app_advert_images';
    }
}
