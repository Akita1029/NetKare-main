<?php

namespace App\Service\PhotoLayout;

use App\Entity\AlbumPhoto;

interface PhotoLayoutInterface
{
    /**
     * @param AlbumPhoto[] $albumPhotos
     */
    public function create(array $albumPhotos): string;
}
