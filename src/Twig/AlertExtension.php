<?php

namespace App\Twig;

use App\Util\AlertUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AlertExtension extends AbstractExtension
{
    public function __construct(private AlertUtil $util)
    {
    }

    public function getFunctions()
    {
        return [new TwigFunction('app_has_alert', [$this->util, 'verify'])];
    }
}
