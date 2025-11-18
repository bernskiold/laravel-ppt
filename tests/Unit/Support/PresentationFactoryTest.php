<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Presentation\Presentation;
use BernskioldMedia\LaravelPpt\Registries\Brandings;
use BernskioldMedia\LaravelPpt\Registries\SlideMasters;
use BernskioldMedia\LaravelPpt\SlideMasters\Title;
use BernskioldMedia\LaravelPpt\Support\PresentationFactory;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Clear registries before each test
    Brandings::clear();
    SlideMasters::clear();

    // Register a test branding
    Brandings::register([
        'TestBrand' => Branding::class,
    ]);
});

it('creates presentation with branding from registry', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: 'TestBrand',
        slides: []
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
});

it('creates presentation with branding class name', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: []
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
});

it('throws exception for invalid branding', function () {
    PresentationFactory::create(
        title: 'Test',
        branding: 'NonExistentBrand',
        slides: []
    );
})->throws(InvalidArgumentException::class, 'not found in registry');

it('creates presentation with slides using master names from registry', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => 'Title',
                'data' => ['title' => 'Test Slide'],
            ],
        ]
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
});

it('creates presentation with slides using master class names', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => Title::class,
                'data' => ['title' => 'Test Slide'],
            ],
        ]
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
});

it('throws exception for missing master key in slide config', function () {
    PresentationFactory::create(
        title: 'Test',
        branding: Branding::class,
        slides: [
            ['data' => ['title' => 'Test']],
        ]
    );
})->throws(InvalidArgumentException::class, "missing required 'master' key");

it('throws exception for missing data key in slide config', function () {
    PresentationFactory::create(
        title: 'Test',
        branding: Branding::class,
        slides: [
            ['master' => 'Title'],
        ]
    );
})->throws(InvalidArgumentException::class, "missing required 'data' key");

it('throws exception for non-existent master', function () {
    PresentationFactory::create(
        title: 'Test',
        branding: Branding::class,
        slides: [
            [
                'master' => 'NonExistentMaster',
                'data' => [],
            ],
        ]
    );
})->throws(InvalidArgumentException::class, 'not found in registry');

it('saves presentation and returns file information', function () {
    Storage::fake('local');

    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => 'Title',
                'data' => ['title' => 'Test'],
            ],
        ]
    );

    $result = PresentationFactory::buildAndSave(
        presentation: $presentation,
        filename: 'test-presentation'
    );

    expect($result)->toHaveKeys(['filename', 'path', 'absolute_path', 'disk']);
    expect($result['filename'])->toBe('test-presentation.pptx');
    expect($result['disk'])->toBe('local');
});

it('creates and saves presentation in one step', function () {
    Storage::fake('local');

    $result = PresentationFactory::createAndSave(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => 'Title',
                'data' => ['title' => 'Test Slide'],
            ],
        ]
    );

    expect($result)->toHaveKeys(['filename', 'path', 'absolute_path', 'disk']);
});

it('accepts custom presentation options', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [],
        width: 1920,
        height: 1080
    );

    expect($presentation->width)->toBe(1920);
    expect($presentation->height)->toBe(1080);
});

it('accepts custom save options', function () {
    Storage::fake('custom-disk');

    $presentation = PresentationFactory::create(
        title: 'Test',
        branding: Branding::class,
        slides: []
    );

    $result = PresentationFactory::buildAndSave(
        presentation: $presentation,
        filename: 'custom-file',
        disk: 'custom-disk',
        directory: 'custom-dir'
    );

    expect($result['disk'])->toBe('custom-disk');
    expect($result['path'])->toContain('custom-dir');
});

it('generates UUID filename when none provided', function () {
    Storage::fake('local');

    $presentation = PresentationFactory::create(
        title: 'Test',
        branding: Branding::class,
        slides: []
    );

    $result = PresentationFactory::buildAndSave(
        presentation: $presentation
    );

    expect($result['filename'])->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.pptx$/');
});
