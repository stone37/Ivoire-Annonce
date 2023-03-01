<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NotificationWidgetController extends AbstractController
{
    public function show(): Response
    {
        return $this->render('site/menu/_notification.html.twig');
    }
}
