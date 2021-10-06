<?php

namespace App\Model;

class Weather
{
    private string $scale;

    private string $city;

    private string $date;

    private array $predictions = [];

    public function getScale(): string
    {
        return $this->scale;
    }

    public function setScale(string $scale): void
    {
        $this->scale = $scale;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getPredictions(): array
    {
        return $this->predictions;
    }

    public function setPredictions(array $predictions): void
    {
        $this->predictions = $predictions;
    }
}
