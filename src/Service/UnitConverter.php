<?php

namespace App\Service;

class UnitConverter
{
    public function mmToPx($mm): int
    {
        return round($mm * (300 / 25.4));
    }
}
