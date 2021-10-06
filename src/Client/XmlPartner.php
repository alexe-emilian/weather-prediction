<?php

namespace App\Client;

use App\Factory\Prediction;
use App\Service\PartnerDataCache;
use App\Service\TemperatureScaleConverter;

class XmlPartner implements PartnerInputInterface
{
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

        $fileData = simplexml_load_file($this->projectDir. '/resources/temps.xml');
        $partnerTemperatureScale = (string) $fileData->attributes()['scale'];

        $predictions = [];
        foreach ($fileData->prediction as $prediction) {
            $convertedPredictionValue = TemperatureScaleConverter::convert(
                (int) $prediction->value,
                $partnerTemperatureScale,
                $temperatureScale
            );
            $predictions[] = Prediction::make($prediction->time, $convertedPredictionValue);
        }

        $this->partnerDataCache->setData($cacheKey, $predictions);

        return $predictions;
    }

    private function buildCacheKey(string $city, \DateTime $date, string $temperatureScale): string
    {
        return sprintf(
            'xml_partner_%s_%s_%s',
            strtolower($city),
            $date->format('Y-m-d'),
            strtolower($temperatureScale)
        );
    }
}
