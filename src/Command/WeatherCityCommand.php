<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'get and show weather for city and country.',
)]
class WeatherCityCommand
{
    public function __construct(
        private WeatherUtil $weatherUtil
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Country Code (ex. PL)')] string $country,
        #[Argument(description: 'City Name')] string $city,
    ): int {
        $forecasts = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);

        if (empty($forecasts)) {
            $io->error(sprintf('No match for location with country "%s" and city "%s"', $country, $city));
            return Command::FAILURE;
        }

        if (empty($forecasts)) {
            $io->warning(sprintf('No measurments for location: %s (%s)', $city, $country));
            return Command::SUCCESS;
        }

        // Wyświetlamy nagłówek i tabelę z prognozą pogody
        $io->title(sprintf('Weather report for: %s, %s', $city, $country));

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
