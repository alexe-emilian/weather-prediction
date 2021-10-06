<?php

namespace App\Service;

use App\Exception\TemperatureScaleConversionException;

class TemperatureScaleConverter
{
    private const CELSIUS = 'celsius';

    private const FAHRENHEIT = 'fahrenheit';

    public static function convert(int $value, string $sourceScale, string $targetScale): int
    {
        if (strtolower($sourceScale) === strtolower($targetScale)) {
            return $value;
        }

        if (strtolower($sourceScale) === self::CELSIUS) {
            return self::convertFromCelsius($value, $targetScale);
        }

        if (strtolower($sourceScale) === self::FAHRENHEIT) {
            return self::convertFromFahrenheit($value, $targetScale);
        }

        throw new TemperatureScaleConversionException();
    }

    private static function convertFromCelsius(int $value, string $targetScale): int
    {
        if (strtolower($targetScale) === self::FAHRENHEIT) {
            return ($value * 1.8) + 32;
        }

        throw new TemperatureScaleConversionException();
    }

    private static function convertFromFahrenheit(int $value, string $targetScale): int
    {
        if (strtolower($targetScale) === self::CELSIUS) {
            return ($value - 32) / 1.8;
        }

        throw new TemperatureScaleConversionException();
    }
}
