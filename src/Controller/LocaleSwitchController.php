<?php

namespace App\Controller;

use App\Context\StorageBasedLocaleContext;
use App\Provider\LocaleProvider;
use App\Service\StorageBasedLocaleSwitcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;


class LocaleSwitchController extends AbstractController
{
    public function __construct(
        private StorageBasedLocaleContext $localeContext,
        private LocaleProvider $localeProvider,
        private StorageBasedLocaleSwitcher $localeSwitcher
    )
    {
    }

    public function show(): Response
    {
        return $this->render('site/menu/_localeSwitch.html.twig', [
            'active' => $this->localeContext->getLocaleCode(),
            'locales' => $this->localeProvider->getAvailableLocalesCodes(),
        ]);
    }

    #[Route(path: '/switch-locale/{code}', name: 'app_switch_locale')]
    public function switch(Request $request, ?string $code = null): Response
    {
        if (null === $code) {
            $code = $this->localeProvider->getDefaultLocaleCode();
        }

        if (!in_array($code, $this->localeProvider->getAvailableLocalesCodes(), true)) {
            throw new HttpException(Response::HTTP_NOT_ACCEPTABLE, sprintf('The locale code "%s" is invalid.', $code));
        }

        return $this->localeSwitcher->handle($request, $code);
    }

    #[Route(path: '/locale/get', name: 'app_locale_get', options: ['expose' => true])]
    public function ajax(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Resource introuvable');
        }

        $locales = $this->localeProvider->getAvailableLocalesCodes();
        $active = $this->localeContext->getLocaleCode();

        $render = $this->render('site/menu/_localeSwitchModal.html.twig', [
            'locales' => $locales,
            'active' => $active
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }
}
