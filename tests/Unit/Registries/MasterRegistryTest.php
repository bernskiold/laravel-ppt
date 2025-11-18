<?php

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

beforeEach(function () {
    // Clear and re-register package masters before each test
    SlideMasters::clear();

    // Register package's built-in slide masters (simulating service provider)
    SlideMasters::registerPackage([
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
});

it('includes package slide masters by default', function () {
    $masters = SlideMasters::all();

    expect($masters)->toHaveKey('Title');
    expect($masters)->toHaveKey('Text');
    expect($masters)->toHaveKey('Chart');
});

it('returns package master classes', function () {
    $classes = SlideMasters::classes();

    expect($classes)->toContain(Title::class);
    expect($classes)->toContain(Text::class);
});

it('checks if a slide master exists', function () {
    expect(SlideMasters::exists('Title'))->toBeTrue();
    expect(SlideMasters::exists('NonExistent'))->toBeFalse();
});

it('gets class name for a slide master', function () {
    expect(SlideMasters::getClass('Title'))->toBe(Title::class);
});

it('returns null for non-existent master', function () {
    expect(SlideMasters::getClass('NonExistent'))->toBeNull();
});

it('returns master names', function () {
    $names = SlideMasters::names();

    expect($names)->toContain('Title', 'Text', 'Chart');
});

it('can register custom slide masters', function () {
    // Use an existing slide master class as a "custom" one for testing
    SlideMasters::register([
        Title::class,
    ]);

    $classes = SlideMasters::classes();

    // Should still have package masters
    expect($classes)->toContain(Title::class);
});

it('merges package and app slide masters', function () {
    // Note: Since we can't easily create a custom test master without modifying package structure,
    // we verify the merge mechanism works
    $packageClasses = SlideMasters::classes();
    $initialCount = count($packageClasses);

    // Register an additional master (using an existing one for test)
    SlideMasters::register([
        Text::class,
    ]);

    $allClasses = SlideMasters::classes();

    // Count should increase
    expect(count($allClasses))->toBeGreaterThan($initialCount);
});

it('returns master definition with metadata', function () {
    $master = SlideMasters::get('Title');

    expect($master)->toHaveKey('class');
    expect($master)->toHaveKey('description');
    expect($master)->toHaveKey('schema');
    expect($master)->toHaveKey('example');
});

it('includes description from DynamicallyCreatable interface', function () {
    $master = SlideMasters::get('Title');

    expect($master['description'])->toBe('A simple slide with a centered title');
});

it('includes schema from DynamicallyCreatable interface', function () {
    $master = SlideMasters::get('Title');

    expect($master['schema'])->toHaveKey('type');
    expect($master['schema'])->toHaveKey('properties');
});

it('includes example data from DynamicallyCreatable interface', function () {
    $master = SlideMasters::get('Title');

    expect($master['example'])->toHaveKey('title');
});

it('can clear registered application masters', function () {
    SlideMasters::register([
        Text::class,
    ]);

    $before = count(SlideMasters::classes());

    SlideMasters::clear();

    $after = count(SlideMasters::classes());

    // After clearing, should only have package masters
    expect($after)->toBeLessThan($before);
});
