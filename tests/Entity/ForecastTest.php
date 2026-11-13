<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Forecast;
use PHPUnit\Framework\Attributes\DataProvider;

class ForecastTest extends TestCase
{
    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(float $celsius, float $expectedFahrenheit): void
    {
        $forecast = new Forecast();

        $forecast->setTemperature($celsius);
        $this->assertEquals($expectedFahrenheit, $forecast->getFahrenheit());
    }

    public static function dataGetFahrenheit(): array
    {
        return [
            [0.0, 32.0],
            [-100.0, -148.0],
            [100.0, 212.0],
            [0.5, 32.9],
            [20.0, 68.0],
            [-40.0, -40.0],
            [37.0, 98.6],
            [10.0, 50.0],
            [-10.0, 14.0],
            [25.5, 77.9],
        ];
    }
}
