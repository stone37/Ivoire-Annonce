<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('La catégorie n\'a pas été trouvée ! Veuillez sélectionner une catégorie.');
    }
}
