<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RequestStack;

class AddClassActiveUtil
{
    public function __construct(private RequestStack $request)
    {
    }

    public function verify($routesToCheck): bool
    {
        $currentRoute = $this->request->getMainRequest()->get('_route');

        if ($routesToCheck == $currentRoute) {
            return true;
        }

        return false;
    }
}