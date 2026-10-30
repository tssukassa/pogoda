<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Location;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;

final class WeatherController extends AbstractController
{
    /*#[Route('/weather/{id}', name: 'app_weather')]
    public function city(): Response
    {
        return $this->render('weather/city.html.twig', [
            'controller_name' => 'WeatherController',
        ]);
    }
*/
    #[Route('/weather/{name}/{country}', name: 'app_weather', defaults: ['country' => null])]
    public function city(
        string $name,
        ?string $country,
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository
    ): Response
    {
        $criteria = ['name' => ucfirst(strtolower($name))];
        if ($country) {
            $criteria['country'] = strtoupper($country);
        }

        $location = $locationRepository->findOneBy($criteria);

        if (!$location) {
            throw $this->createNotFoundException('Nie znaleziono takiej lokalizacji.');        }

        $forecast = $forecastRepository->findBy(['location' => $location]);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'forecast' => $forecast,
        ]);
    }
}
