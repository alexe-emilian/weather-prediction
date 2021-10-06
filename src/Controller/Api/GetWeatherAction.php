<?php

namespace App\Controller\Api;

use App\Exception\InvalidDateException;
use App\Exception\WeatherException;
use App\Service\WeatherPrediction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class GetWeatherAction
{
    private WeatherPrediction $weatherPrediction;

    private SerializerInterface $serializer;

    public function __construct(
        WeatherPrediction $weatherPrediction,
        SerializerInterface $serializer
    ) {
        $this->weatherPrediction = $weatherPrediction;
        $this->serializer = $serializer;
    }

    public function get(Request $request): JsonResponse
    {
        $city = $request->query->get('city');
        $date = $request->query->get('date');
        $scale = $request->query->get('scale');

        $dateTime = (new \DateTime($date))->setTime(0, 0);

        try {
            $this->validateDate($dateTime);
            $response = $this->weatherPrediction->getData($city, $dateTime, $scale);
        } catch (WeatherException $exception) {
            return new JsonResponse(
                [
                    'error' => [
                        'message' => $exception->getMessage()
                    ]
                ],
                500
            );
        } catch (\Throwable $exception) {
            return new JsonResponse(
                [
                    'error' => [
                        'message' => 'Something went wrong'
                    ]
                ],
                500
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($response, 'json'),
            200,
            [],
            true
        );
    }

    private function validateDate(\DateTime $date): void
    {
        $currentDate = \DateTime::createFromFormat('U', strtotime('now'))->setTime(0, 0);
        $maxDate = \DateTime::createFromFormat('U', strtotime('+10 days'))->setTime(0, 0);

        if ($date < $currentDate || $date > $maxDate) {
            throw new InvalidDateException(
                sprintf(
                    'Cannot retrieve weather for the requested date. Weather can only be retrieved for dates up until %s, starting from the current day',
                    $maxDate->format('Y-m-d')
                )
            );
        }
    }
}
