<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SecurityWidgetController extends AbstractController
{
    public function show(): Response
    {
        return $this->render('site/menu/_security.html.twig');
    }
}
