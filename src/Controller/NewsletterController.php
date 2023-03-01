<?php

namespace App\Controller;

use App\Handler\NewsletterSubscriptionHandler;
use App\Repository\NewsletterDataRepository;
use App\Validator\NewsletterValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    public function __construct(
        private NewsletterSubscriptionHandler $handler,
        private NewsletterValidator $validator,
        private NewsletterDataRepository $repository
    )
    {
    }

    #[Route(path: '/newsletter/subscribe', name: 'app_newsletter_subscribe', options: ['expose' => true])]
    public function subscribe(Request $request): JsonResponse
    {
        $email = $request->request->get('email');

        $errors = $this->validator->validate($email);

        if (!$this->isCsrfTokenValid('newsletter', $request->request->get('_token'))) {
            $errors[] = 'Le jeton CSRF est invalide.';
        }

        if (count($errors) === 0) {
            $response = $this->handler->subscribe($email);

            if ($response) {
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Vous êtes bien inscrit à notre newsletter.',
                ]);
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Vous êtes déjà inscrit à notre newsletter.',
                ]);
            }
        }

        return new JsonResponse(['success' => false, 'errors' => json_encode($errors)]);
    }

    #[Route(path: '/newsletter/unsubscribe', name: 'app_newsletter_unsubscribe')]
    public function unsubscribe(Request $request): RedirectResponse
    {
        $email = $request->query->get('email');
        $data = $this->repository->findOneBy(['email' => $email]);

        if (!$data) {
            $this->addFlash('error', 'Cette adresse n\'est pas notre base de donnée');
            return $this->redirectToRoute('app_home');
        }

        $this->handler->unsubscribe($data);

        $this->addFlash('success', 'Votre adresse mail a été supprimer de notre newsletter');

        return $this->redirectToRoute('app_home');
    }
}

