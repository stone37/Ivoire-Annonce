<?php


namespace App\Controller;


use App\Controller\Traits\ControllerTrait;
use App\Entity\Advert;
use App\Service\Composer;
use App\Service\Sender;
//use ReCaptcha\ReCaptcha;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private Composer $composer,
        private Sender $sender,
        private ValidatorInterface $validator,
        /*private ReCaptcha $captcha*/
    )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/messages/{id}/new', name: 'app_message_thread_new', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function newThread(Request $request, Advert $advert): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        $message = ($this->composer->newThread())
            ->setSender($this->getUserOrThrow())
            ->setAdvert($advert)
            ->addRecipient($advert->getOwner())
            ->setBody($request->request->get('content'))
            ->getMessage();

        $errors = $this->validator->validate($message);

        if (!$this->isCsrfTokenValid('advert-message', $request->request->get('_token'))) {
            $errors[] = 'Le jeton CSRF est invalide.';
        }

        /*if (!$reCaptcha->verify($request->request->get('recaptchaToken'))->isSuccess()) {
            $errors[] = 'Erreur pendant l\'envoi de votre message';
        }*/

        if (!count($errors)) {
            $this->sender->send($message);

            return new JsonResponse(['success' => true, 'message' => 'Votre message a été envoyer']);
        }

        $data = [];

        foreach ($errors as $error) {
            $data[] = $error->getMessage();
        }

        return new JsonResponse(['success' => false, 'errors' => json_encode($data)]);
    }
}
