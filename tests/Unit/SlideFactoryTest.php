<?php

use BernskioldMedia\LaravelPpt\SlideMasters\BulletPoints;
use BernskioldMedia\LaravelPpt\SlideMasters\Chart;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartTitle;
use BernskioldMedia\LaravelPpt\SlideMasters\FourUp;
use BernskioldMedia\LaravelPpt\SlideMasters\Text;
use BernskioldMedia\LaravelPpt\SlideMasters\Title;
use BernskioldMedia\LaravelPpt\SlideMasters\TitleSubtitle;
use BernskioldMedia\LaravelPpt\SlideMasters\TwoUp;
use BernskioldMedia\LaravelPpt\Support\SlideFactory;

it('can create a simple title slide', function () {
    $data = [
        'title' => 'Welcome',
    ];

    $slide = SlideFactory::create(Title::class, $data);

    expect($slide)->toBeInstanceOf(Title::class);
});

it('can create a title subtitle slide', function () {
    $data = [
        'title' => 'Annual Report',
        'subtitle' => 'Fiscal Year 2024',
    ];

    $slide = SlideFactory::create(TitleSubtitle::class, $data);

    expect($slide)->toBeInstanceOf(TitleSubtitle::class);
});

it('can create a text slide', function () {
    $data = [
        'text' => 'This is some content',
    ];

    $slide = SlideFactory::create(Text::class, $data);

    expect($slide)->toBeInstanceOf(Text::class);
});

it('can create a bullet points slide', function () {
    $data = [
        'title' => 'Key Features',
        'bulletPoints' => [
            'Feature 1',
            'Feature 2',
            'Feature 3',
        ],
    ];

    $slide = SlideFactory::create(BulletPoints::class, $data);

    expect($slide)->toBeInstanceOf(BulletPoints::class);
});

it('can create a chart slide', function () {
    $data = [
        'chartType' => 'Bar',
        'chartData' => [
            'series' => [
                [
                    'label' => '2024',
                    'data' => ['Q1' => 100, 'Q2' => 120],
                ],
            ],
        ],
    ];

    $slide = SlideFactory::create(Chart::class, $data);

    expect($slide)->toBeInstanceOf(Chart::class);
});

it('can create a chart title slide', function () {
    $data = [
        'title' => 'Q4 Performance',
        'chartType' => 'Bar',
        'chartData' => [
            'series' => [
                [
                    'label' => '2024',
                    'data' => ['Q1' => 100, 'Q2' => 120],
                ],
            ],
        ],
    ];

    $slide = SlideFactory::create(ChartTitle::class, $data);

    expect($slide)->toBeInstanceOf(ChartTitle::class);
});

it('can create a two up slide', function () {
    $data = [
        'title' => 'Our Approach',
        'boxes' => [
            [
                'title' => 'Strategy',
                'description' => 'Our strategy description',
            ],
            [
                'title' => 'Execution',
                'description' => 'Our execution description',
            ],
        ],
    ];

    $slide = SlideFactory::create(TwoUp::class, $data);

    expect($slide)->toBeInstanceOf(TwoUp::class);
});

it('can create a four up slide', function () {
    $data = [
        'title' => 'Our Core Values',
        'boxes' => [
            [
                'title' => 'Innovation',
                'description' => 'Innovation description',
            ],
            [
                'title' => 'Quality',
                'description' => 'Quality description',
            ],
            [
                'title' => 'Collaboration',
                'description' => 'Collaboration description',
            ],
            [
                'title' => 'Integrity',
                'description' => 'Integrity description',
            ],
        ],
    ];

    $slide = SlideFactory::create(FourUp::class, $data);

    expect($slide)->toBeInstanceOf(FourUp::class);
});

it('handles default parameter values', function () {
    $data = [
        'title' => 'Test Title',
    ];

    $slide = SlideFactory::create(BulletPoints::class, $data);

    expect($slide)->toBeInstanceOf(BulletPoints::class);
});

it('throws exception for missing required parameters', function () {
    $data = [
        'subtitle' => 'Only subtitle, no title',
    ];

    SlideFactory::create(TitleSubtitle::class, $data);
})->throws(InvalidArgumentException::class, "Missing required parameter 'title'");

it('throws exception for non-dynamically-creatable slide', function () {
    $data = [
        'title' => 'Test',
    ];

    SlideFactory::create(\BernskioldMedia\LaravelPpt\Presentation\BaseSlide::class, $data);
})->throws(InvalidArgumentException::class, 'does not implement DynamicallyCreatable interface');

it('throws exception when chart data is missing', function () {
    $data = [
        'chartType' => 'Bar',
        // Missing chartData
    ];

    SlideFactory::create(Chart::class, $data);
})->throws(InvalidArgumentException::class, 'Missing chart data for parameter');
