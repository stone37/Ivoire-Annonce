<?php

namespace App\Twig;

use App\Util\PathUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPathExtension extends AbstractExtension
{
    public function __construct(private PathUtil $util)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploads_path', [$this->util, 'uploadsPath']),
            new TwigFunction('image_url', [$this->util, 'imageUrl']),
            new TwigFunction('image_asset', [$this->util, 'imageAsset']),
        ];
    }
}
