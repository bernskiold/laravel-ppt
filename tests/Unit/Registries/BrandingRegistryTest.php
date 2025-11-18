<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Registries\BrandingRegistry;

beforeEach(function () {
    // Clear any registered brandings before each test
    BrandingRegistry::clear();
});

it('returns empty array by default', function () {
    expect(BrandingRegistry::all())->toBeEmpty();
});

it('returns empty names array by default', function () {
    expect(BrandingRegistry::names())->toBeEmpty();
});

it('can register a single branding', function () {
    BrandingRegistry::register([
        'TestBrand' => Branding::class,
    ]);

    expect(BrandingRegistry::all())->toHaveKey('TestBrand');
    expect(BrandingRegistry::getClass('TestBrand'))->toBe(Branding::class);
});

it('can register multiple brandings', function () {
    BrandingRegistry::register([
        'Brand1' => Branding::class,
        'Brand2' => Branding::class,
    ]);

    expect(BrandingRegistry::names())->toContain('Brand1', 'Brand2');
});

it('checks if a branding exists', function () {
    BrandingRegistry::register([
        'ExistingBrand' => Branding::class,
    ]);

    expect(BrandingRegistry::exists('ExistingBrand'))->toBeTrue();
    expect(BrandingRegistry::exists('NonExistentBrand'))->toBeFalse();
});

it('returns null for non-existent branding class', function () {
    expect(BrandingRegistry::getClass('NonExistent'))->toBeNull();
});

it('can clear all registered brandings', function () {
    BrandingRegistry::register([
        'TestBrand' => Branding::class,
    ]);

    expect(BrandingRegistry::all())->not->toBeEmpty();

    BrandingRegistry::clear();

    expect(BrandingRegistry::all())->toBeEmpty();
});

it('merges multiple registrations', function () {
    BrandingRegistry::register([
        'Brand1' => Branding::class,
    ]);

    BrandingRegistry::register([
        'Brand2' => Branding::class,
    ]);

    expect(BrandingRegistry::names())->toContain('Brand1', 'Brand2');
});

it('overwrites branding with same name', function () {
    $originalClass = Branding::class;
    $newClass = Branding::class;

    BrandingRegistry::register([
        'Brand' => $originalClass,
    ]);

    BrandingRegistry::register([
        'Brand' => $newClass,
    ]);

    expect(BrandingRegistry::getClass('Brand'))->toBe($newClass);
});
