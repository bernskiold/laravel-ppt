<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Enums\WriterType;
use BernskioldMedia\LaravelPpt\Presentation\Presentation;
use BernskioldMedia\LaravelPpt\Registries\Brandings;
use BernskioldMedia\LaravelPpt\Registries\SlideMasters;
use BernskioldMedia\LaravelPpt\SlideMasters\Blank;
use BernskioldMedia\LaravelPpt\SlideMasters\BlankWithTitle;
use BernskioldMedia\LaravelPpt\SlideMasters\BlankWithTitleSubtitle;
use BernskioldMedia\LaravelPpt\SlideMasters\BulletPoints;
use BernskioldMedia\LaravelPpt\SlideMasters\Chart;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartSquare;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartText;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartTitle;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartTitles;
use BernskioldMedia\LaravelPpt\SlideMasters\FourUp;
use BernskioldMedia\LaravelPpt\SlideMasters\SixUp;
use BernskioldMedia\LaravelPpt\SlideMasters\Text;
use BernskioldMedia\LaravelPpt\SlideMasters\Title;
use BernskioldMedia\LaravelPpt\SlideMasters\TitleSubtitle;
use BernskioldMedia\LaravelPpt\SlideMasters\TwoUp;
use BernskioldMedia\LaravelPpt\Support\PresentationFactory;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Clear registries before each test
    Brandings::clear();
    SlideMasters::clear();

    // Register built-in slide masters (simulating service provider)
    SlideMasters::register([
        Blank::class,
        BlankWithTitle::class,
        BlankWithTitleSubtitle::class,
        BulletPoints::class,
        Chart::class,
        ChartSquare::class,
        ChartText::class,
        ChartTitle::class,
        ChartTitles::class,
        FourUp::class,
        SixUp::class,
        Text::class,
        Title::class,
        TitleSubtitle::class,
        TwoUp::class,
    ]);

    // Register a test branding
    Brandings::register([
        Branding::class,
    ]);
});

it('creates presentation with branding from registry', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: 'Branding',
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

it('creates presentation with slides using master keys from registry', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => 'title',
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
            ['master' => 'title'],
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
                'master' => 'title',
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
                'master' => 'title',
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

it('creates presentation with PDF writer type', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [],
        writerType: WriterType::PDF
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
    expect($presentation->getWriterType())->toBe(WriterType::PDF);
});

it('creates presentation with HTML writer type', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [],
        writerType: WriterType::HTML
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
    expect($presentation->getWriterType())->toBe(WriterType::HTML);
});

it('creates presentation with ODPresentation writer type', function () {
    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [],
        writerType: WriterType::ODPresentation
    );

    expect($presentation)->toBeInstanceOf(Presentation::class);
    expect($presentation->getWriterType())->toBe(WriterType::ODPresentation);
});

it('saves presentation as HTML with correct extension', function () {
    Storage::fake('local');

    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => 'title',
                'data' => ['title' => 'Test'],
            ],
        ]
    );

    $result = PresentationFactory::buildAndSave(
        presentation: $presentation,
        filename: 'test-presentation',
        inRootFolder: true,
        writerType: WriterType::HTML
    );

    expect($result['filename'])->toBe('test-presentation.html');
    expect($result['path'])->toBe('test-presentation.html');
    Storage::disk('local')->assertExists('test-presentation.html');
});

it('saves presentation as ODPresentation with correct extension', function () {
    Storage::fake('local');

    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: [
            [
                'master' => 'title',
                'data' => ['title' => 'Test'],
            ],
        ]
    );

    $result = PresentationFactory::buildAndSave(
        presentation: $presentation,
        filename: 'test-presentation',
        inRootFolder: true,
        writerType: WriterType::ODPresentation
    );

    expect($result['filename'])->toBe('test-presentation.odp');
    expect($result['path'])->toBe('test-presentation.odp');
    Storage::disk('local')->assertExists('test-presentation.odp');
});

it('defaults to PowerPoint2007 when no writer type specified', function () {
    Storage::fake('local');

    $presentation = PresentationFactory::create(
        title: 'Test Presentation',
        branding: Branding::class,
        slides: []
    );

    $result = PresentationFactory::buildAndSave(
        presentation: $presentation,
        filename: 'test-default'
    );

    expect($result['filename'])->toBe('test-default.pptx');
    expect($result['path'])->toContain('test-default.pptx');
});
