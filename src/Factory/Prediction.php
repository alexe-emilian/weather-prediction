<?php

namespace App\Factory;

use App\Model\Prediction as PredictionModel;

class Prediction
{
    public static function make(string $time, int $value): PredictionModel
    {
        $prediction = new PredictionModel();
        $prediction->setTime($time);
        $prediction->setValue($value);

        return $prediction;
    }
}
