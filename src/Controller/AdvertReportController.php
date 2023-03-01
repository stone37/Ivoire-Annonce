<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Report;
use App\Entity\Settings;
use App\Mailing\Mailer;
//use ReCaptcha\ReCaptcha;
use App\Manager\SettingsManager;
use App\Repository\ReportRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdvertReportController extends AbstractController
{
    private ?Settings $settings;

    public function __construct(
        private ReportRepository $repository,
        private ValidatorInterface $validator,
        private Mailer $mailer,
        SettingsManager $manager
        /*private ReCaptcha $captcha*/
    )
    {
        $this->settings = $manager->get();
    }

    #[Route(path: '/advert/{id}/report', name: 'app_advert_report_create', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function create(Request $request, Advert $advert): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) $this->createNotFoundException('Resource introuvable');

        $report = (new Report())
            ->setCreatedAt(new DateTime())
            ->setAdvert($advert)
            ->setContent($request->request->get('reportContent') ?? null)
            ->setReason($request->request->get('reason')?? null);

        if ($this->getUser()) {
            $report->setEmail($this->getUser()->getEmail());
        } else {
            $report->setEmail($request->request->get('reportEmail'));
        }

        $errors = $this->validator->validate($report);

        if (!$this->isCsrfTokenValid('advert-report', $request->request->get('_token'))) {
            $errors[] = 'Le jeton CSRF est invalide.';
        }

        /*if (!$reCaptcha->verify($request->request->get('recaptchaToken'))->isSuccess()) {
            $errors[] = 'Erreur pendant l\'envoi de votre message';
        }*/

        if (!count($errors)) {
            $this->repository->add($report, true);

            $sender = $this->mailer->createEmail('mails/advert/report.twig', ['report' => $report])
                ->to($this->settings->getEmail())
                ->subject($this->settings->getName() . ' | Signalement d\'une annonce');

            $this->mailer->send($sender);

            return new JsonResponse(['success' => true, 'message' => 'Merci pour votre signalement']);
        }

        $data = [];

        foreach ($errors as $error) {
            $data[] = $error->getMessage();
        }

        return new JsonResponse(['success' => false, 'errors' => json_encode($data)]);
    }
}
