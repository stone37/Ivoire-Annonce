<?php

namespace App\Util;

use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PathUtil
{
    private $resizeKey;
    private UrlGeneratorInterface$router;
    private UploaderHelper $helper;

    public function __construct(
        UploaderHelper $helper,
        UrlGeneratorInterface $router,
        ParameterBagInterface $parameterBag
    ) {
        $this->helper = $helper;
        $this->router = $router;
        $this->resizeKey = $parameterBag->get('image_resize_key');
    }

    public function uploadsPath(string $path): string
    {
        return '/uploads/'.trim($path, '/');
    }

    public function imageUrl(?object $entity, ?int $width = null, ?int $height = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        $parameters['w'] = $width;
        $parameters['h'] = $height;
        $parameters['fm'] = 'pjpg';

        $path = $this->helper->asset($entity);

        if (!$path) {
            return "";
        }

        if ('png' === substr($path, -3)) {
            $parameters['fm'] = 'png';
        }

        $parameters['s'] = SignatureFactory::create($this->resizeKey)->generateSignature($path, $parameters);
        $parameters['path'] = ltrim($path, '/');

        return $this->router->generate('app_image_resizer', $parameters, $referenceType);
    }

    public function imageAsset(string $path, ?int $width = null, ?int $height = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): ?string
    {
        $parameters['w'] = $width;
        $parameters['h'] = $height;
        $parameters['fm'] = 'pjpg';

        if (!$path) {
            return "";
        }

        if ('png' === substr($path, -3)) {
            $parameters['fm'] = 'png';
        }

        $parameters['s'] = SignatureFactory::create($this->resizeKey)->generateSignature($path, $parameters);
        $parameters['path'] = ltrim($path, '/');

        return $this->router->generate('app_image_resizer', $parameters, $referenceType);
    }
}
