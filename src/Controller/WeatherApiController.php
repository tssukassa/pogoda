<?php

namespace App\Controller;

use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api', methods: ['GET'])]
    public function index(
        #[MapQueryParameter] ?string $country,
        #[MapQueryParameter] ?string $city,
        #[MapQueryParameter] ?string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
        WeatherUtil $weatherUtil,
    ): Response
    {
        $forecasts = $weatherUtil->getWeatherForCountryAndCity($country, $city);

        if ($twig) {
            $template = $format === 'csv'
                ? 'weather_api/index.csv.twig'
                : 'weather_api/index.json.twig';

            return $this->render($template, [
                'city' => $city,
                'country' => $country,
                'forecasts' => $forecasts,
            ]);
        }

        //  CSV
        if ($format === 'csv') {
            $lines = array_map(fn($m) => implode(',', [
                $city,
                $country,
                $m->getDate()->format('Y-m-d'),
                $m->getTemperature(),
                $m->getFahrenheit()
            ]), $forecasts);

            array_unshift($lines, 'city,country,date,celsius, fahrenheit');

            return new Response(implode("\n", $lines), 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
            ]);
        }

        //  JSON
        return $this->json([
            'city' => $city,
            'country' => $country,
            'forecasts' => array_map(fn($m) => [
                'date' => $m->getDate()->format('Y-m-d'),
                'celsius' => $m->getTemperature(),
                'fahrenheit' => $m->getFahrenheit(),
                'wind' => $m->getWind(),
                'humidity' => $m->getHumidity(),
                'precipitation' => $m->getPrecipitation()
            ], $forecasts),
        ]);
    }
}
