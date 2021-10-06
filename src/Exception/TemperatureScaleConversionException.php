<?php

namespace App\Exception;

class TemperatureScaleConversionException extends WeatherException
{
    protected $message = 'Could not convert to target temperature scale';
}
