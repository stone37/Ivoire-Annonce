<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Controller\Traits\UploadTrait;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UploadController extends AbstractController
{
    use ControllerTrait;
    use UploadTrait;

    #[Route(path: '/upload/image', name: 'app_image_upload_add', options: ['expose' => true])]
    public function add(Request $request): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        $files = $this->getFiles($request->files);

        foreach ($files as $file) {
            try {
                try {
                    $this->upload($file, $request->getSession());
                } catch (FileException) {}
            } catch (UploadException) {}
        }

        return new JsonResponse();
    }

    #[Route(path: '/upload/image/{pos}/delete', name: 'app_image_upload_delete', requirements: ['pos' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, $pos): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        if (!$request->getSession()->has($this->provideKey())) {
            return $this->createNotFoundException('Bad request');
        }

        $data = $request->getSession()->get($this->provideKey());

        $system = new Filesystem();
        $finder = new Finder();

        try {
            $finder->in($this->getFindPath($request->getSession()))->name(''.key($data[$pos]).'');
        } catch (InvalidArgumentException) {
            $finder->append([]);
        }

        foreach ($finder as $file) {
            $system->remove((string) $file->getRealPath());
            array_splice($data, $pos, 1);
            $request->getSession()->set($this->provideKey(), $data);
        }

        return new JsonResponse();
    }

    #[Route(path: '/upload/image/principale', name: 'app_image_upload_principale', options: ['expose' => true])]
    public function principale(Request $request): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        if (!$request->getSession()->has($this->provideKey())) {
            return $this->createNotFoundException('Bad request');
        }

        $pos = $request->query->get('pos');
        $data = $request->getSession()->get($this->provideKey());

        $array = [];

        foreach ($data as $cle => $values) {
            if ($cle == $pos) {
                foreach ($values as $key => $value) {
                    $array[] = [$key => 1];
                }
            } else {
                foreach ($values as $key => $value) {
                    $array[] = [$key => 0];
                }
            }
        }

        $request->getSession()->set($this->provideKey(), $array);

        return new JsonResponse();
    }
}

