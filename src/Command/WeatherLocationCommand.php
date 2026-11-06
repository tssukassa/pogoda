<?php
declare(strict_types=1);

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'get and show weather for id.',
)]
class WeatherLocationCommand
{
    public function __construct(
        private WeatherUtil $weatherUtil,
        private LocationRepository $locationRepository
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Location ID')] int $id,
    ): int {
        $location = $this->locationRepository->find($id);

        if (!$location) {
            $io->error(sprintf('No match fo location with ID: %d', $id));
            return Command::FAILURE;
        }

        $forecasts = $this->weatherUtil->getWeatherForLocation($location);

        if (empty($forecasts)) {
            $io->warning(sprintf('No measurments for location: %s', $location->getName()));
            return Command::SUCCESS;
        }

        $io->title(sprintf('Weather report for: %s (%s)', $location->getName(), $location->getCountry()));

        $rows = [];
        foreach ($forecasts as $forecast) {
            $rows[] = [
                $forecast->getDate()?->format('Y-m-d H:i'),
                $forecast->getTemperature() . ' °C',
                $forecast->getWind() . ' km/h',
                $forecast->getHumidity() . '%',
                $forecast->getPrecipitation() . 'mm',
            ];
        }

        $io->table(['Date', 'Temperature', 'Wind', 'Humidity', 'Precipitation'], $rows);

        return Command::SUCCESS;
    }
}
