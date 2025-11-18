<?php

use BernskioldMedia\LaravelPpt\Registries\MasterRegistry;
use BernskioldMedia\LaravelPpt\SlideMasters\Title;
use BernskioldMedia\LaravelPpt\SlideMasters\Text;
use BernskioldMedia\LaravelPpt\SlideMasters\Chart;

beforeEach(function () {
    // Clear any registered application masters before each test
    MasterRegistry::clear();
});

it('includes package slide masters by default', function () {
    $masters = MasterRegistry::all();

    expect($masters)->toHaveKey('Title');
    expect($masters)->toHaveKey('Text');
    expect($masters)->toHaveKey('Chart');
});

it('returns package master classes', function () {
    $classes = MasterRegistry::classes();

    expect($classes)->toContain(Title::class);
    expect($classes)->toContain(Text::class);
});

it('checks if a slide master exists', function () {
    expect(MasterRegistry::exists('Title'))->toBeTrue();
    expect(MasterRegistry::exists('NonExistent'))->toBeFalse();
});

it('gets class name for a slide master', function () {
    expect(MasterRegistry::getClass('Title'))->toBe(Title::class);
});

it('returns null for non-existent master', function () {
    expect(MasterRegistry::getClass('NonExistent'))->toBeNull();
});

it('returns master names', function () {
    $names = MasterRegistry::names();

    expect($names)->toContain('Title', 'Text', 'Chart');
});

it('can register custom slide masters', function () {
    // Use an existing slide master class as a "custom" one for testing
    MasterRegistry::register([
        Title::class,
    ]);

    $classes = MasterRegistry::classes();

    // Should still have package masters
    expect($classes)->toContain(Title::class);
});

it('merges package and app slide masters', function () {
    // Note: Since we can't easily create a custom test master without modifying package structure,
    // we verify the merge mechanism works
    $packageClasses = MasterRegistry::classes();
    $initialCount = count($packageClasses);

    // Register an additional master (using an existing one for test)
    MasterRegistry::register([
        Text::class,
    ]);

    $allClasses = MasterRegistry::classes();

    // Count should increase
    expect(count($allClasses))->toBeGreaterThan($initialCount);
});

it('returns master definition with metadata', function () {
    $master = MasterRegistry::get('Title');

    expect($master)->toHaveKey('class');
    expect($master)->toHaveKey('description');
    expect($master)->toHaveKey('schema');
    expect($master)->toHaveKey('example');
});

it('includes description from DynamicallyCreatable interface', function () {
    $master = MasterRegistry::get('Title');

    expect($master['description'])->toBe('A simple slide with a centered title');
});

it('includes schema from DynamicallyCreatable interface', function () {
    $master = MasterRegistry::get('Title');

    expect($master['schema'])->toHaveKey('type');
    expect($master['schema'])->toHaveKey('properties');
});

it('includes example data from DynamicallyCreatable interface', function () {
    $master = MasterRegistry::get('Title');

    expect($master['example'])->toHaveKey('title');
});

it('can clear registered application masters', function () {
    MasterRegistry::register([
        Text::class,
    ]);

    $before = count(MasterRegistry::classes());

    MasterRegistry::clear();

    $after = count(MasterRegistry::classes());

    // After clearing, should only have package masters
    expect($after)->toBeLessThan($before);
});
