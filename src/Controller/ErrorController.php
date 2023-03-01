<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    public function body(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/_body.html.twig', []);
    }
}
