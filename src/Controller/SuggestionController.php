<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Suggestion;
use App\Form\SuggestionType;
use App\Repository\SuggestionRepository;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SuggestionController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private SuggestionRepository $repository,
        private ReCaptcha $reCaptcha
    )
    {
    }

    #[Route(path: '/suggestion', name: 'app_suggestion_index')]
    public function index(Request $request): RedirectResponse|Response
    {
        $suggestion = new Suggestion();

        if ($this->getUser()) {
            $user = $this->getUser();
            $suggestion->setEmail($user->getEmail());
            $suggestion->setName($user->getFirstname());
        }

        $form = $this->createForm(SuggestionType::class, $suggestion, ['action' => $this->generateUrl('app_suggestion_index')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()) {
                $this->repository->add($suggestion, true);

                $this->addFlash('success', 'Merci pour votre suggestion');
            } else {
                $this->addFlash('error', 'Erreur pendant l\'envoi de votre suggestion');
            }

            return $this->redirectToRoute('app_suggestion_index');
        }

        return $this->render('site/suggestion/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
