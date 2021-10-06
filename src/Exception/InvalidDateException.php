<?php

namespace App\Exception;

class InvalidDateException extends WeatherException
{
    protected $message = 'Cannot retrieve weather for the requested date.';
}
