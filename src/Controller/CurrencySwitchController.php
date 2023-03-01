<?php

namespace App\Controller;

use App\Context\StorageBasedCurrencyContext;
use App\Entity\Currency;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\CurrencyRepository;
use App\Storage\CurrencyStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CurrencySwitchController extends AbstractController
{
    private ?Settings $settings;

    public function __construct(
        private StorageBasedCurrencyContext $currencyContext,
        private CurrencyStorage $currencyStorage,
        private CurrencyRepository $currencyRepository,
        SettingsManager $settingsManager
    )
    {
        $this->settings = $settingsManager->get();
    }

    public function show(): Response
    {
        $availableCurrencies = array_map(
            function (Currency $currency) {return $currency->getCode();},
            $this->currencyRepository->findAll()
        );

        return $this->render('site/menu/_currencySwitch.html.twig', [
            'active' => $this->currencyContext->getCurrencyCode($this->settings),
            'currencies' => $availableCurrencies
        ]);
    }

    #[Route(path: '/switch-currency/{code}', name: 'app_switch_currency')]
    public function switch(Request $request, ?string $code = null): RedirectResponse
    {
        $this->currencyStorage->set($this->settings, $code);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }

    #[Route(path: '/currency/get', name: 'app_currency_get', options: ['expose' => true])]
    public function ajax(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Resource introuvable');
        }

        $availableCurrencies = array_map(
            function (Currency $currency) {return $currency->getCode();},
            $this->currencyRepository->findAll()
        );

        $currencies = $availableCurrencies;
        $active = $this->currencyContext->getCurrencyCode($this->settings);

        $render = $this->render('site/menu/_currencySwitchModal.html.twig', [
            'currencies' => $currencies,
            'active' => $active
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }
}

