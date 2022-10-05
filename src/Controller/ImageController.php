<?php

namespace App\Controller;

use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    private function mmToPx($mm): int
    {
        return round($mm * (300 / 25.4));
    }

    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {
        $imagine = new Imagine;

        $collage = $imagine->create(new Box($this->mmToPx(150), $this->mmToPx(100)));

        $x = 4;
        $y = 1;

        for ($i = 0; $i < 4; $i++) {
            $photo = $imagine->open($this->getParameter('project_directory') . '/einstein.jpeg');
            $photo->resize(new Box($this->mmToPx(35), $this->mmToPx(46.7)));

            $collage->paste($photo, new Point($this->mmToPx($x), $this->mmToPx($y)));

            $x = $x + 35 + (2 / 3);
        }

        $x = 4;
        $y = 49.3;

        for ($i = 0; $i < 4; $i++) {
            $photo = $imagine->open($this->getParameter('project_directory') . '/einstein.jpeg');
            $photo->resize(new Box($this->mmToPx(35), $this->mmToPx(46.7)));

            $collage->paste($photo, new Point($this->mmToPx($x), $this->mmToPx($y)));

            $x = $x + 35 + (2 / 3);
        }

        $palette = new RGB();

        $font = $imagine->font($this->getParameter('project_directory') . '/tahoma.ttf', 20, $palette->color('#000'));

        // dd($font);

        $collage->draw()->text('1/A SINIFI', $font, new Point(10, 10));
        $collage->draw()->text('MEHMET İHSAN MERMERCİ MESLEKİ VE TEKNİK ANADOLU LİSESİ', $font, new Point(20, 20));

        $collage->draw()->text('MUHAMMED DURAN', $font, new Point(40, $this->mmToPx(96)));
        $collage->draw()->text('SİBEL TAŞÇI', $font, new Point(80, $this->mmToPx(96)));
        $collage->draw()->text('EBRU KAYA', $font, new Point(120, $this->mmToPx(96)));
        $collage->draw()->text('SELAMİ ÖZ', $font, new Point(160, $this->mmToPx(96)));

        dd($collage);

        return $this->render('');
    }

    #[Route('/layout', name: 'app_layout')]
    public function layout(): Response {
        return $this->render('yearbook/school.html.twig');
    }
}
