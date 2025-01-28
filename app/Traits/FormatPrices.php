<?php

namespace App\Traits;

trait FormatPrices
{
    public function formatPrice($value)
    {
        return number_format($value, 2);
    }
}