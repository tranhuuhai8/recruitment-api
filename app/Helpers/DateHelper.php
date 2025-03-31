<?php

namespace App\Helpers;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\App;

class DateHelper
{
    /**
     * @param  $date
     * @param  string $format
     * @return string
     */
    public static function parseDate($date, string $format = 'Y-m-d H:i:s')
    {
        try {
            if (!$date) {
                return '';
            }

            return Carbon::createFromFormat($format, $date)->diffForHumans();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param  $date
     * @return string
     */
    public static function formattedDate($date, $isBirthday = false)
    {
        if (!$date) {
            return '';
        }
        if (App::getLocale() == 'jp') {
            if ($isBirthday) {
                return Carbon::parse($date . '/01')->format('Y年m月');
            }
            return Carbon::parse($date)->format('Y年m月d日');
        }
        return Carbon::parse($date)->toFormattedDateString();
    }

    public static function parseOnlyDate($date)
    {
        if (!$date) {
            return '';
        }

        return Carbon::parse($date)->format('Y/m/d');
    }

    public static function parseOnlyHI($date)
    {
        if (!$date) {
            return '';
        }

        return Carbon::parse($date)->format('H:i');
    }

    public static function parseDateBe($date, $format = 'Y-m-d H:i:s')
    {
        try {
            if (!$date) {
                return null;
            }
            return Carbon::parse($date)->format($format);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function parseDateMonth($date)
    {
        if (!$date) {
            return '';
        }

        return Carbon::parse($date)->format('Y/n/j');
    }

    /**
     * getStartEndMonth
     *
     * @param  string $date
     * @param  bool   $tillEndOfMonth
     * @return array
     */
    public static function getStartEndMonth(string $date, bool $tillEndOfMonth = true): array
    {
        return [
            Carbon::parse($date)->startOfMonth(),
            $tillEndOfMonth ? Carbon::parse($date)->endOfMonth() : now(),
        ];
    }
}
