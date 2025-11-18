<?php

namespace BernskioldMedia\LaravelPpt\Support;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Components\Charts\Bar;
use BernskioldMedia\LaravelPpt\Components\Charts\Column;
use BernskioldMedia\LaravelPpt\Components\Charts\Line;
use BernskioldMedia\LaravelPpt\Components\Charts\Radar;
use BernskioldMedia\LaravelPpt\Components\Charts\Scatter;
use InvalidArgumentException;

class ChartFactory
{
    /**
     * Create a chart component from type and data.
     *
     * @param  string  $type  Chart type (Bar, StackedBar, Line, etc.)
     * @param  array  $chartData  Chart data with 'series' array
     */
    public static function create(string $type, array $chartData): ChartComponent
    {
        $data = $chartData['series'] ?? [];

        return match ($type) {
            'Bar' => Bar::make($data),
            'StackedBar' => Bar::make($data)->stacked(),
            'PercentageStackedBar' => Bar::make($data)->percentageStacked(),
            'Column' => Column::make($data),
            'StackedColumn' => Column::make($data)->stacked(),
            'PercentageStackedColumn' => Column::make($data)->percentageStacked(),
            'Line' => Line::make($data),
            'Radar' => Radar::make($data),
            'Scatter' => Scatter::make($data),
            default => throw new InvalidArgumentException(
                "Chart type '{$type}' is not supported. Available types: ".
                implode(', ', static::supportedTypes())
            ),
        };
    }

    /**
     * Get list of supported chart types.
     *
     * @return array<string>
     */
    public static function supportedTypes(): array
    {
        return [
            'Bar',
            'StackedBar',
            'PercentageStackedBar',
            'Column',
            'StackedColumn',
            'PercentageStackedColumn',
            'Line',
            'Radar',
            'Scatter',
        ];
    }
}
