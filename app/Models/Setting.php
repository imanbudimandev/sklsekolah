<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function formatLetterNumber($format, $number, $year, $startRaw = null)
    {
        $autoPad = 0;
        if ($startRaw !== null && preg_match('/^0+\d/', $startRaw)) {
            $autoPad = strlen($startRaw);
        }

        return preg_replace_callback('/\[NUMBER(?::(\d+))?\]/', function ($m) use ($number, $autoPad) {
            $width = isset($m[1]) ? (int) $m[1] : $autoPad;
            return $width > 0 ? str_pad($number, $width, '0', STR_PAD_LEFT) : $number;
        }, str_replace('[YEAR]', $year, $format));
    }
}
