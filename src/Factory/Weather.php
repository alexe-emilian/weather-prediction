<?php

namespace App\Factory;

use App\Model\Prediction;
use App\Model\Weather as WeatherModel;

class Weather
{
    public static function make(string $city, \DateTime $date, string $temperatureScale, array $predictions): WeatherModel
    {
        $weather = new WeatherModel();
        $weather->setCity($city);
        $weather->setDate($date->format('Y-m-d'));
        $weather->setScale($temperatureScale);
        $weather->setPredictions($predictions);

        return $weather;
    }
}
