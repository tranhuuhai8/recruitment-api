<?php

namespace App\Helpers;

class CommonHelper
{
    /**
     * randomNumber
     *
     * @param  int  $min
     * @param  int  $max
     * @param  int  $number
     * @param  bool $flag
     * @return string
     */
    public static function randomNumber(int $min, int $max, int $number, $flag = true): string
    {
        return $flag ? (string) rand($min, $max) : str_pad(rand($min, $max), $number, '0', STR_PAD_LEFT);
    }
}
