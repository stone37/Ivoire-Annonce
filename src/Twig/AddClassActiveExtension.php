<?php

namespace App\Twig;

use App\Util\AddClassActiveUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddClassActiveExtension extends AbstractExtension
{
    public function __construct(private AddClassActiveUtil $util)
    {
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('isActive', [$this->util, 'verify'])
        );
    }
}