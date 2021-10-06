<?php

namespace App\Tests\Service;

use App\Client\PartnerInputInterface;
use App\Factory\Prediction;
use App\Service\WeatherPrediction;
use PHPUnit\Framework\TestCase;

class WeatherPredictionTest extends TestCase
{
    public function testPredictionDataIsRetrieved(): void
    {
        $city = 'Amsterdam';
        $date = new \DateTime();
        $temperatureScale = 'celsius';

        $firstPartnerInput = $this->createMock(PartnerInputInterface::class);
        $firstPredictionFirstPartner = Prediction::make('00:00', 10);
        $secondPredictionFirstPartner = Prediction::make('01:00', 12);
        $firstPartnerInput
            ->expects($this->once())
            ->method('getPredictions')
            ->willReturn([$firstPredictionFirstPartner, $secondPredictionFirstPartner]);

        $secondPartnerInput = $this->createMock(PartnerInputInterface::class);
        $firstPredictionSecondPartner = Prediction::make('00:00', 12);
        $secondPredictionSecondPartner = Prediction::make('01:00', 14);
        $secondPartnerInput
            ->expects($this->once())
            ->method('getPredictions')
            ->willReturn([$firstPredictionSecondPartner, $secondPredictionSecondPartner]);

        $weatherPrediction = new WeatherPrediction([$firstPartnerInput, $secondPartnerInput]);
        $firstExpectedPrediction = Prediction::make('00:00', 11);
        $secondExpectedPrediction = Prediction::make('01:00', 13);
        $expectedPredictions = [
            $firstExpectedPrediction,
            $secondExpectedPrediction,
        ];

        $result = $weatherPrediction->getData($city, $date, $temperatureScale);

        $this->assertEquals($city, $result->getCity());
        $this->assertEquals($date->format('Y-m-d'), $result->getDate());
        $this->assertEquals($temperatureScale, $result->getScale());
        $this->assertEquals($expectedPredictions, $result->getPredictions());
    }
}
