<?php

namespace App\Service\StudentLayout;

use App\Entity\Classroom;
use Exception;
use Imagine\Filter\Advanced\Border;
use Imagine\Filter\Transformation;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Imagick\Imagine;

class ExecutiveAlbum56 extends StudentLayout implements StudentLayoutInterface
{
    // Grid
    const GRID_ROW = 7;
    const GRID_COL = 8;

    // Collage
    const COLLAGE_W = 2480;
    const COLLAGE_H = 3508;

    const COLLAGE_PADDING_TOP = 104;
    const COLLAGE_PADDING_LEFT = 105;

    // Image
    const IMAGE_W = 270;
    const IMAGE_H = 360;

    const IMAGE_MARGIN_TOP = 100;
    const IMAGE_MARGIN_LEFT = 16;

    public function create(Classroom $classroom, array $students): string
    {
        if (empty($students)) {
            throw new Exception();
        }

        if (count($students) > 30) {
            throw new Exception();
        }

        $imagine = new Imagine;

        $collageBox = new Box(self::COLLAGE_W, self::COLLAGE_H);
        $collageCenter = new Center($collageBox);
        $collage = $imagine->create($collageBox);

        $palette = new RGB();

        $fontSm = $imagine->font($this->projectDirectory . '/assets/fonts/tahoma.ttf', 24, $palette->color('#000'));
        $fontLg = $imagine->font($this->projectDirectory . '/assets/fonts/tahoma.ttf', 42, $palette->color('#000'));

        $text = $classroom->getSchool()->getName();
        $textBox = $fontLg->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $fontLg, new Point($collageCenter->getX() - $textCenter->getX(), 22));

        $text = $classroom->getName();
        $textBox = $fontLg->box($text);
        $textCenter = new Center($textBox);

        $collage->draw()->text($text, $fontLg, new Point(self::COLLAGE_W - $textBox->getWidth() - 100, 22));

        $transformation = new Transformation();
        $transformation->add(new Border($palette->color('#999')));

        $x = self::COLLAGE_PADDING_LEFT;
        $y = self::COLLAGE_PADDING_TOP;

        $i = 0;

        foreach ($students as $student) {
            if ($student->getPhotos()->isEmpty()) {
                $photo = $imagine->open($this->projectDirectory . '/assets/images/no-photo.jpeg');
            } else {
                $photo = $imagine->load($this->objectStorage->getObject($student->getPhotos()->first()->getImage()->getName())['Body']->getContents());
            }

            $photo->resize(new Box(self::IMAGE_W, self::IMAGE_H));

            $transformation->apply($photo);

            $collage->paste($photo, new Point($x, $y));

            $text = $fontSm->wrapText($student->getSchoolNumber() . ' ' . $student->getName() . ' ' . $student->getSurname(), self::IMAGE_W);
            $textBox = $fontSm->box($text);
            $textCenter = new Center($textBox);

            $collage->draw()->text($text, $fontSm, new Point($x + (self::IMAGE_W / 2 - $textCenter->getX()), self::IMAGE_H + $y + 10));

            $x = $x + self::IMAGE_W + self::IMAGE_MARGIN_LEFT;

            $i++;

            if ($i % self::GRID_COL === 0) {
                $x = self::COLLAGE_PADDING_LEFT;
                $y = $y + self::IMAGE_H + self::IMAGE_MARGIN_TOP;
            }
        }

        return $collage->get(parent::FORMAT);
    }
}
