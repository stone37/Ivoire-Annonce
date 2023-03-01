<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Exception\TooManyContactException;
use App\Data\ContactData;
use App\Service\ContactService;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Form\ContactType;

class ContactController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private Breadcrumbs $breadcrumbs,
        private ContactService $service,
        private ReCaptcha $captcha
    )
    {
    }

    #[Route(path: '/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Contact');

        $data = new ContactData();
        $form = $this->createForm(ContactType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->captcha->verify($form['recaptchaToken']->getData())->isSuccess()) {
                try {
                    $this->service->send($data, $request);
                } catch (TooManyContactException) {
                    $this->addFlash('error', 'Vous avez fait trop de demandes de contact consécutives.');

                    return $this->redirectToRoute('app_contact');
                }

                $this->addFlash('success', 'Votre demande de contact a été transmis, nous vous répondrons dans les meilleurs délais.');

                return $this->redirectToRoute('app_contact');
            } else {
                $this->addFlash('error', 'Erreur pendant l\'envoi de votre message');

                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('site/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

