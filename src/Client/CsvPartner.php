<?php

namespace App\Client;

use App\Factory\Prediction;
use App\Service\PartnerDataCache;
use App\Service\TemperatureScaleConverter;

class CsvPartner implements PartnerInputInterface
{
    private const SCALE_KEY = 0;

    private const CITY_KEY = 1;

    private const DATE_KEY = 2;

    private const PREDICTION_TIME_KEY = 3;

    private const PREDICTION_VALUE_KEY = 4;

    private string $projectDir;

    private PartnerDataCache $partnerDataCache;

    public function __construct(string $projectDir, PartnerDataCache $partnerDataCache)
    {
        $this->projectDir = $projectDir;
        $this->partnerDataCache = $partnerDataCache;
    }

    /**
     * For the purpose of this assignment, we only read from one document to mock the response that would be sent by
     * the API of a partner.
     *
     * In order to simulate the request below, the data has been cached with a ttl of 60 seconds
     * "The data provided via our partners changes all the time. We've set a threshold of 1 minute to invalidate this data"
     */
    public function getPredictions(string $city, \DateTime $date, string $temperatureScale): array
    {
        $cacheKey = $this->buildCacheKey($city, $date, $temperatureScale);
        $partnerData = $this->partnerDataCache->getData($cacheKey);

        if (!empty($partnerData)) {
            return $partnerData;
        }

        $file = fopen($this->projectDir. '/resources/temps.csv', 'r');

        $this->validateHeaders(fgetcsv($file));

        $predictions = [];
        $partnerTemperatureScale = null;
        while (($line = fgetcsv($file)) !== FALSE) {
            if (!empty($line[self::SCALE_KEY])) {
                $partnerTemperatureScale = $line[self::SCALE_KEY];
            }

            $convertedPredictionValue = TemperatureScaleConverter::convert(
                (int) $line[self::PREDICTION_VALUE_KEY],
                $partnerTemperatureScale,
                $temperatureScale
            );
            $predictions[] = Prediction::make(
                $line[self::PREDICTION_TIME_KEY],
                $convertedPredictionValue
            );
        }
        fclose($file);

        $this->partnerDataCache->setData($cacheKey, $predictions);

        return $predictions;
    }

    private function validateHeaders(array $line): void
    {
        if (
            !strpos($line[self::SCALE_KEY], '"-scale"') ||
            $line[self::CITY_KEY] !== 'city' ||
            $line[self::DATE_KEY] !== 'date' ||
            $line[self::PREDICTION_TIME_KEY] !== 'prediction__time' ||
            $line[self::PREDICTION_VALUE_KEY] !== 'prediction__value'
        ) {
            throw new \RuntimeException('Invalid headers in the provided csv file');
        }
    }

    private function buildCacheKey(string $city, \DateTime $date, string $temperatureScale): string
    {
        return sprintf(
            'csv_partner_%s_%s_%s',
            strtolower($city),
            $date->format('Y-m-d'),
            strtolower($temperatureScale)
        );
    }
}
