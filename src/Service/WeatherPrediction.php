<?php

namespace App\Service;

use App\Client\PartnerInputInterface;
use App\Factory\Prediction as PredictionFactory;
use App\Factory\Weather as WeatherFactory;
use App\Model\Prediction;
use App\Model\Weather as WeatherModel;

class WeatherPrediction
{
    private iterable $partnerWeatherInputSources;

    public function __construct(iterable $partnerWeatherInputSources)
    {
        $this->partnerWeatherInputSources = $partnerWeatherInputSources;
    }

    public function getData(string $city, \DateTime $date, string $temperatureScale): WeatherModel
    {
        $groupedPredictions = $this->getGroupedPredictions($city, $date, $temperatureScale);
        $predictionAverageValues = $this->getAverageValuesForPredictions($groupedPredictions);

        return WeatherFactory::make($city, $date, $temperatureScale, $predictionAverageValues);
    }

    private function getGroupedPredictions(string $city, \DateTime $date, string $temperatureScale): array
    {
        $groupedPredictions = [];
        /** @var PartnerInputInterface $partnerWeatherInputSource */
        foreach ($this->partnerWeatherInputSources as $partnerWeatherInputSource) {
            $predictions = $partnerWeatherInputSource->getPredictions($city, $date, $temperatureScale);
            /** @var Prediction $prediction */
            foreach ($predictions as $prediction) {
                $groupedPredictions[$prediction->getTime()][] = $prediction->getValue();
            }
        }

        return $groupedPredictions;
    }

    private function getAverageValuesForPredictions(array $groupedPredictions): array
    {
        $predictionAverageValues = [];
        foreach ($groupedPredictions as $predictionTime => $groupedPredictionValues) {
            $predictionAverageValue = round(array_sum($groupedPredictionValues) / count($groupedPredictionValues));
            $predictionAverageValues[] = PredictionFactory::make($predictionTime, $predictionAverageValue);
        }

        return $predictionAverageValues;
    }
}
