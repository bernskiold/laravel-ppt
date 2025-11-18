<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Registries\Brandings;

beforeEach(function () {
    // Clear any registered brandings before each test
    Brandings::clear();
});

it('returns empty array by default', function () {
    expect(Brandings::all())->toBeEmpty();
});

it('returns empty names array by default', function () {
    expect(Brandings::names())->toBeEmpty();
});

it('can register a single branding', function () {
    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::all())->toHaveKey('Branding');
    expect(Brandings::getClass('Branding'))->toBe(Branding::class);
});

it('can register multiple brandings', function () {
    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::names())->toContain('Branding');
});

it('checks if a branding exists', function () {
    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::exists('Branding'))->toBeTrue();
    expect(Brandings::exists('NonExistentBrand'))->toBeFalse();
});

it('returns null for non-existent branding class', function () {
    expect(Brandings::getClass('NonExistent'))->toBeNull();
});

it('can clear all registered brandings', function () {
    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::all())->not->toBeEmpty();

    Brandings::clear();

    expect(Brandings::all())->toBeEmpty();
});

it('merges multiple registrations', function () {
    Brandings::register([
        Branding::class,
    ]);

    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::names())->toContain('Branding');
    expect(count(Brandings::all()))->toBe(1); // Same class registered twice only appears once
});

it('overwrites branding with same label', function () {
    Brandings::register([
        Branding::class,
    ]);

    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::getClass('Branding'))->toBe(Branding::class);
    expect(count(Brandings::all()))->toBe(1); // Only one entry for 'Branding'
});

it('can unregister a single branding', function () {
    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::exists('Branding'))->toBeTrue();

    Brandings::unregister([Branding::class]);

    expect(Brandings::exists('Branding'))->toBeFalse();
});

it('can unregister multiple brandings', function () {
    Brandings::register([
        Branding::class,
    ]);

    Brandings::unregister([Branding::class]);

    expect(Brandings::exists('Branding'))->toBeFalse();
});

it('unregister handles non-existent brandings gracefully', function () {
    Brandings::register([
        Branding::class,
    ]);

    // Unregister with a fake class (won't match) and real class
    Brandings::unregister([Branding::class]);

    expect(Brandings::exists('Branding'))->toBeFalse();
    expect(Brandings::all())->toBeEmpty();
});
