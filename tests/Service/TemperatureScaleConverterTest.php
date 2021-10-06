<?php

namespace App\Tests\Service;

use App\Service\TemperatureScaleConverter;
use PHPUnit\Framework\TestCase;

class TemperatureScaleConverterTest extends TestCase
{
    public function testCanConvertValueWithSameSourceAndTarget(): void
    {
        $result = TemperatureScaleConverter::convert(10, 'celsius', 'celsius');

        $this->assertEquals(10, $result);
    }

    public function testCanConvertValueFromCelsiusToFahrenheit(): void
    {
        $result = TemperatureScaleConverter::convert(10, 'celsius', 'fahrenheit');

        $this->assertEquals(50, $result);
    }

    public function testCanConvertValueFromFahrenheitToCelsius(): void
    {
        $result = TemperatureScaleConverter::convert(38, 'fahrenheit', 'celsius');

        $this->assertEquals(3, $result);
    }
}
