<?php

namespace App\Client;

interface PartnerInputInterface
{
    public function getPredictions(string $city, \DateTime $date, string $temperatureScale): array;
}
