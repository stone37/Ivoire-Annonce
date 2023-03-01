<?php

namespace App\Util;

use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbUtil
{
    public function __construct(
        private Breadcrumbs $breadcrumbs,
        private RouterInterface $router
    )
    {
    }

    public function addBreadcrumb($label, $url = '', array $translationParameters = array())
    {
        if (!$this->breadcrumbs->count()) {
            $this->breadcrumbs->addItem('Accueil', $this->router->generate('app_home'));
        }

        $this->breadcrumbs->addItem($label, $url, $translationParameters);
    }
}
