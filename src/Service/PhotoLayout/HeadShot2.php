<?php

namespace App\Service\PhotoLayout;

use Exception;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Imagick\Imagine;

class HeadShot2 extends PhotoLayout implements PhotoLayoutInterface
{
    const COLLAGE_W = 1181;
    const COLLAGE_H = 1772;

    const IMAGE_W = 414;
    const IMAGE_H = 553;

    const IMAGE_1_Y = 10;
    const IMAGE_2_Y = self::IMAGE_1_Y + self::IMAGE_H + 10;

    public function create(array $albumPhotos): string
    {
        if (empty($albumPhotos)) {
            throw new Exception();
        }

        if (count($albumPhotos) > 4) {
            throw new Exception();
        }

        $imagine = new Imagine;

        $collageBox = new Box(self::COLLAGE_W, self::COLLAGE_H);
        $collageCenter = new Center($collageBox);
        $collage = $imagine->create($collageBox);

        $palette = new RGB();

        $font = $imagine->font($this->projectDirectory . '/tahoma.ttf', 30, $palette->color('#000'));

        $text = '1/A SINIFI';
        $textBox = $font->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $font, new Point($collageCenter->getX() - $textCenter->getX(), 5));

        $text = $this->wrapText($font, 'MEHMET İHSAN MERMERCİ MESLEKİ VE TEKNİK ANADOLU LİSESİ', self::COLLAGE_W);
        $textBox = $font->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $font, new Point($collageCenter->getX() - $textCenter->getX(), self::COLLAGE_H - $textBox->getHeight() - 5));

        $collage->rotate(-90);

        $x = 50;

        foreach ($albumPhotos as $albumPhoto) {
            // if (!$albumPhoto->getStudent()) {
            //     continue;
            // }

            $photoBox = new Box(self::IMAGE_W, self::IMAGE_H);
            $photoCenter = new Center($photoBox);
            $photo = $imagine->load($this->objectStorage->getObject($albumPhoto->getImage()->getAsset()->getName())['Body']->getContents());
            $photo->resize($photoBox);

            $collage->paste($photo, new Point($x, self::IMAGE_1_Y));
            $collage->paste($photo, new Point($x, self::IMAGE_2_Y));

            $text = $this->wrapText($font, $albumPhoto->getStudent()->getName() . ' ' . $albumPhoto->getStudent()->getSurname(), self::IMAGE_W);
            $textBox = $font->box($text);
            $textCenter = new Center($textBox);

            $collage->draw()->text($text, $font, new Point($x + $photoCenter->getX() - $textCenter->getX(), self::IMAGE_H + self::IMAGE_2_Y + 2));

            $x = $x + self::IMAGE_W + 4;
        }

        $collage->save($this->projectDirectory . '/x.jpg');

        dd('done');
    }
}
