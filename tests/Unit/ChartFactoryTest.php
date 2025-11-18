<?php

use BernskioldMedia\LaravelPpt\Components\Charts\Bar;
use BernskioldMedia\LaravelPpt\Components\Charts\Column;
use BernskioldMedia\LaravelPpt\Components\Charts\Line;
use BernskioldMedia\LaravelPpt\Components\Charts\Radar;
use BernskioldMedia\LaravelPpt\Components\Charts\Scatter;
use BernskioldMedia\LaravelPpt\Support\ChartFactory;

it('can create a bar chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('Bar', $chartData);

    expect($chart)->toBeInstanceOf(Bar::class);
});

it('can create a stacked bar chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('StackedBar', $chartData);

    expect($chart)->toBeInstanceOf(Bar::class);
});

it('can create a percentage stacked bar chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('PercentageStackedBar', $chartData);

    expect($chart)->toBeInstanceOf(Bar::class);
});

it('can create a column chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('Column', $chartData);

    expect($chart)->toBeInstanceOf(Column::class);
});

it('can create a line chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('Line', $chartData);

    expect($chart)->toBeInstanceOf(Line::class);
});

it('can create a radar chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('Radar', $chartData);

    expect($chart)->toBeInstanceOf(Radar::class);
});

it('can create a scatter chart', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    $chart = ChartFactory::create('Scatter', $chartData);

    expect($chart)->toBeInstanceOf(Scatter::class);
});

it('throws exception for unsupported chart type', function () {
    $chartData = [
        'series' => [
            [
                'label' => '2024',
                'data' => ['Q1' => 100, 'Q2' => 120],
            ],
        ],
    ];

    ChartFactory::create('InvalidType', $chartData);
})->throws(InvalidArgumentException::class, "Chart type 'InvalidType' is not supported");

it('returns list of supported chart types', function () {
    $types = ChartFactory::supportedTypes();

    expect($types)->toBeArray()
        ->and($types)->toContain('Bar', 'StackedBar', 'PercentageStackedBar', 'Column', 'Line', 'Radar', 'Scatter');
});
