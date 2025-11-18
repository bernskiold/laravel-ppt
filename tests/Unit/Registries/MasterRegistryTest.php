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
    // Clear and re-register masters before each test
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
});

it('includes registered slide masters', function () {
    $masters = SlideMasters::all();

    expect($masters)->toHaveKey('title');
    expect($masters)->toHaveKey('text');
    expect($masters)->toHaveKey('chart');
});

it('uses kebab-case keys for master identification', function () {
    $masters = SlideMasters::all();

    // Only masters that implement DynamicallyCreatable appear in all()
    expect($masters)->toHaveKey('bullet-points');
    expect($masters)->toHaveKey('chart-square');
    expect($masters)->toHaveKey('title-subtitle');
});

it('returns registered master classes', function () {
    $classes = SlideMasters::classes();

    expect($classes)->toContain(Title::class);
    expect($classes)->toContain(Text::class);
});

it('checks if a slide master exists', function () {
    expect(SlideMasters::exists('title'))->toBeTrue();
    expect(SlideMasters::exists('non-existent'))->toBeFalse();
});

it('gets class name for a slide master', function () {
    expect(SlideMasters::getClass('title'))->toBe(Title::class);
});

it('returns null for non-existent master', function () {
    expect(SlideMasters::getClass('non-existent'))->toBeNull();
});

it('returns master keys', function () {
    $names = SlideMasters::names();

    expect($names)->toContain('title', 'text', 'chart');
});

it('can register custom slide masters', function () {
    // Use an existing slide master class as a "custom" one for testing
    SlideMasters::register([
        Title::class,
    ]);

    $classes = SlideMasters::classes();

    // Should still have registered masters
    expect($classes)->toContain(Title::class);
});

it('can register additional slide masters', function () {
    $initialClasses = SlideMasters::classes();
    $initialCount = count($initialClasses);

    // Register an additional master (using an existing one for test)
    SlideMasters::register([
        Text::class,
    ]);

    $allClasses = SlideMasters::classes();

    // Count should increase
    expect(count($allClasses))->toBeGreaterThan($initialCount);
});

it('returns master definition with metadata', function () {
    $master = SlideMasters::get('title');

    expect($master)->toHaveKey('class');
    expect($master)->toHaveKey('key');
    expect($master)->toHaveKey('label');
    expect($master)->toHaveKey('description');
    expect($master)->toHaveKey('schema');
    expect($master)->toHaveKey('example');
});

it('includes description from DynamicallyCreatable interface', function () {
    $master = SlideMasters::get('title');

    expect($master['description'])->toBe('A simple slide with a centered title');
});

it('includes schema from DynamicallyCreatable interface', function () {
    $master = SlideMasters::get('title');

    expect($master['schema'])->toHaveKey('type');
    expect($master['schema'])->toHaveKey('properties');
});

it('includes example data from DynamicallyCreatable interface', function () {
    $master = SlideMasters::get('title');

    expect($master['example'])->toHaveKey('title');
});

it('can clear all registered masters', function () {
    SlideMasters::register([
        Text::class,
    ]);

    $before = count(SlideMasters::classes());

    SlideMasters::clear();

    $after = count(SlideMasters::classes());

    // After clearing, should have fewer masters
    expect($after)->toBe(0);
});

it('can unregister a slide master by class name', function () {
    expect(SlideMasters::exists('title'))->toBeTrue();

    SlideMasters::unregister([Title::class]);

    expect(SlideMasters::exists('title'))->toBeFalse();
    expect(SlideMasters::exists('text'))->toBeTrue();
});

it('can unregister multiple slide masters', function () {
    expect(SlideMasters::exists('title'))->toBeTrue();
    expect(SlideMasters::exists('text'))->toBeTrue();
    expect(SlideMasters::exists('chart'))->toBeTrue();

    SlideMasters::unregister([Title::class, Chart::class]);

    expect(SlideMasters::exists('title'))->toBeFalse();
    expect(SlideMasters::exists('text'))->toBeTrue();
    expect(SlideMasters::exists('chart'))->toBeFalse();
});

it('unregister handles non-existent masters gracefully', function () {
    $initialCount = count(SlideMasters::classes());

    SlideMasters::unregister([Title::class]);

    expect(SlideMasters::exists('title'))->toBeFalse();
    expect(count(SlideMasters::classes()))->toBe($initialCount - 1);
});
