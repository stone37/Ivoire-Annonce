<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Form\SettingsModuleType;
use App\Form\SettingsType;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class SettingsController extends AbstractController
{
    public function __construct(private SettingsRepository $repository)
    {
    }

    #[Route(path: '/settings', name: 'app_admin_settings_index')]
    public function index(Request $request): Response
    {
        $settings = $this->repository->getSettings();

        if (null === $settings) {
            $settings = new Settings();
        }

        $form = $this->createForm(SettingsType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->add($settings, true);

            $this->addFlash('success', 'Les paramètres du site ont été mise à jour');

            return $this->redirectToRoute('app_admin_settings_index');
        }

        return $this->render('admin/settings/index.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    #[Route(path: '/settings/modules', name: 'app_admin_settings_module_index')]
    public function module(Request $request): RedirectResponse|Response
    {
        $settings = $this->repository->getSettings();

        if (!$settings) {
            return $this->redirectToRoute('app_admin_settings_index');
        }

        $form = $this->createForm(SettingsModuleType::class, $settings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->flush();

            $this->addFlash('success', 'Les paramètres des modules ont été mise à jour');

            return $this->redirectToRoute('app_admin_settings_module_index');
        }

        return $this->render('admin/settings/module.html.twig', [
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }
}

