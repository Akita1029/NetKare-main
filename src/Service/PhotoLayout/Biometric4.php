<?php

namespace App\Service\PhotoLayout;

use Exception;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Imagick\Imagine;

class Biometric4 extends PhotoLayout implements PhotoLayoutInterface
{
    const COLLAGE_W = 1181;
    const COLLAGE_H = 1772;

    const IMAGE_W = 590;
    const IMAGE_H = 709;

    const IMAGE_1_Y = 90;
    const IMAGE_2_Y = 889;

    public function create(array $albumPhotos): string
    {
        if (empty($albumPhotos)) {
            throw new Exception();
        }

        if (count($albumPhotos) > 1) {
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

        $collage->draw()->text($text, $font, new Point($collageCenter->getX() - $textCenter->getX(), 90 / 2 - $textCenter->getY()));

        $photo = $imagine->load($this->objectStorage->getObject($albumPhotos[0]->getImage()->getAsset()->getName())['Body']->getContents());
        $photo->resize(new Box(self::IMAGE_W, self::IMAGE_H));

        $y = self::IMAGE_1_Y;

        $collage->paste($photo, new Point(0, $y));
        $collage->paste($photo, new Point($collageCenter->getX(), $y));

        $text = $this->wrapText($font, $albumPhotos[0]->getStudent()->getName() . ' ' . $albumPhotos[0]->getStudent()->getSurname(), self::IMAGE_W);
        $textBox = $font->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $font, new Point($collageCenter->getX() - $textCenter->getX(), self::IMAGE_H + $y + 10));

        $y = self::IMAGE_2_Y;

        $collage->paste($photo, new Point(0, $y));
        $collage->paste($photo, new Point($collageCenter->getX(), $y));

        $text = $this->wrapText($font, $albumPhotos[0]->getStudent()->getName() . ' ' . $albumPhotos[0]->getStudent()->getSurname(), self::IMAGE_W);
        $textBox = $font->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $font, new Point($collageCenter->getX() - $textCenter->getX(), self::IMAGE_H + $y + 10));

        $text = $this->wrapText($font, 'MEHMET İHSAN MERMERCİ MESLEKİ VE TEKNİK ANADOLU LİSESİ', self::COLLAGE_W);
        $textBox = $font->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $font, new Point($collageCenter->getX() - $textCenter->getX(), self::COLLAGE_H - $textBox->getHeight() - 10));

        $collage->rotate(-90);

        return $collage->get(parent::FORMAT);
    }
}
