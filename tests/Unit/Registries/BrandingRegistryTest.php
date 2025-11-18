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

it('can register a single branding with key', function () {
    Brandings::register([
        'TestBrand' => Branding::class,
    ]);

    expect(Brandings::all())->toHaveKey('TestBrand');
    expect(Brandings::getClass('TestBrand'))->toBe(Branding::class);
});

it('can register a single branding with class only', function () {
    Brandings::register([
        Branding::class,
    ]);

    expect(Brandings::all())->toHaveKey('Branding');
    expect(Brandings::getClass('Branding'))->toBe(Branding::class);
});

it('can register multiple brandings', function () {
    Brandings::register([
        'Brand1' => Branding::class,
        'Brand2' => Branding::class,
    ]);

    expect(Brandings::names())->toContain('Brand1', 'Brand2');
});

it('checks if a branding exists', function () {
    Brandings::register([
        'ExistingBrand' => Branding::class,
    ]);

    expect(Brandings::exists('ExistingBrand'))->toBeTrue();
    expect(Brandings::exists('NonExistentBrand'))->toBeFalse();
});

it('returns null for non-existent branding class', function () {
    expect(Brandings::getClass('NonExistent'))->toBeNull();
});

it('can clear all registered brandings', function () {
    Brandings::register([
        'TestBrand' => Branding::class,
    ]);

    expect(Brandings::all())->not->toBeEmpty();

    Brandings::clear();

    expect(Brandings::all())->toBeEmpty();
});

it('merges multiple registrations', function () {
    Brandings::register([
        'Brand1' => Branding::class,
    ]);

    Brandings::register([
        'Brand2' => Branding::class,
    ]);

    expect(Brandings::names())->toContain('Brand1', 'Brand2');
});

it('overwrites branding with same name', function () {
    $originalClass = Branding::class;
    $newClass = Branding::class;

    Brandings::register([
        'Brand' => $originalClass,
    ]);

    Brandings::register([
        'Brand' => $newClass,
    ]);

    expect(Brandings::getClass('Brand'))->toBe($newClass);
});

it('can unregister a single branding', function () {
    Brandings::register([
        'Brand1' => Branding::class,
        'Brand2' => Branding::class,
    ]);

    expect(Brandings::exists('Brand1'))->toBeTrue();

    Brandings::unregister(['Brand1']);

    expect(Brandings::exists('Brand1'))->toBeFalse();
    expect(Brandings::exists('Brand2'))->toBeTrue();
});

it('can unregister multiple brandings', function () {
    Brandings::register([
        'Brand1' => Branding::class,
        'Brand2' => Branding::class,
        'Brand3' => Branding::class,
    ]);

    Brandings::unregister(['Brand1', 'Brand3']);

    expect(Brandings::exists('Brand1'))->toBeFalse();
    expect(Brandings::exists('Brand2'))->toBeTrue();
    expect(Brandings::exists('Brand3'))->toBeFalse();
});

it('unregister handles non-existent brandings gracefully', function () {
    Brandings::register([
        'Brand1' => Branding::class,
    ]);

    Brandings::unregister(['NonExistent', 'Brand1']);

    expect(Brandings::exists('Brand1'))->toBeFalse();
    expect(Brandings::all())->toBeEmpty();
});
