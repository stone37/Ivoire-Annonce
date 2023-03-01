<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private ReviewRepository $repository,
        private ReCaptcha $reCaptcha,
        private PaginatorInterface $paginator
    )
    {
    }

    #[Route(path: '/avis', name: 'app_review_index')]
    public function index(Request $request): RedirectResponse|Response
    {
        $review = new Review();

        if ($this->getUser()) {
            $user = $this->getUser();
            $review->setEmail($user->getEmail());
            $review->setName($user->getFirstname());
        }

        $form = $this->createForm(ReviewType::class, $review, ['action' => $this->generateUrl('app_review_index')]);

        $qb = $this->repository->getEnabled();
        $reviews = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 20);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->reCaptcha->verify($form['recaptchaToken']->getData())->isSuccess()) {
                $this->repository->add($review, true);

                $this->addFlash('success', 'Merci pour votre témoignage');
            } else {
                $this->addFlash('error', 'Erreur pendant l\'envoi de votre témoignage');
            }

            return $this->redirectToRoute('app_review_index');
        }

        return $this->render('site/avis/index.html.twig', [
            'form' => $form->createView(),
            'reviews' => $reviews
        ]);
    }
}
