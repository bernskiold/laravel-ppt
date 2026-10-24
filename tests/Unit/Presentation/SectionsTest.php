<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Presentation\Presentation;
use BernskioldMedia\LaravelPpt\SlideMasters\Blank;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpPresentation\Section;

function blankSlide(): Blank
{
    return Blank::make(fn () => null);
}

/**
 * @return array<string, int>
 */
function sectionSizes(Presentation $presentation): array
{
    return collect($presentation->document->getAllSections())
        ->mapWithKeys(fn (Section $section) => [$section->getName() => count($section->getSlides())])
        ->all();
}

it('adds no sections unless asked', function () {
    $presentation = Presentation::make('Test', branding: Branding::class)
        ->slides([blankSlide(), blankSlide()]);

    expect($presentation->document->getAllSections())->toBe([]);
})->skip(! Presentation::supportsSections(), 'Needs a PHPPresentation with sections.');

it('puts the slides before the first section in a default one', function () {
    $presentation = Presentation::make('Test', branding: Branding::class)
        ->slides([blankSlide(), blankSlide()])
        ->section('Ford', [blankSlide(), blankSlide(), blankSlide()])
        ->section('Coca-Cola', [blankSlide()]);

    expect(sectionSizes($presentation))->toBe([
        Presentation::DEFAULT_SECTION => 2,
        'Ford' => 3,
        'Coca-Cola' => 1,
    ]);

    $document = $presentation->document;

    expect($document->getSectionOf($document->getSlide(1))->getName())->toBe(Presentation::DEFAULT_SECTION)
        ->and($document->getSectionOf($document->getSlide(2))->getName())->toBe('Ford')
        ->and($document->getSectionOf($document->getSlide(5))->getName())->toBe('Coca-Cola');
})->skip(! Presentation::supportsSections(), 'Needs a PHPPresentation with sections.');

it('starts with no default section when the deck opens on one', function () {
    $presentation = Presentation::make('Test', branding: Branding::class)
        ->section('Ford', [blankSlide()]);

    expect(sectionSizes($presentation))->toBe(['Ford' => 1]);
})->skip(! Presentation::supportsSections(), 'Needs a PHPPresentation with sections.');

it('adds the slides that follow a section to it', function () {
    $presentation = Presentation::make('Test', branding: Branding::class)
        ->section('Ford')
        ->addSlide(blankSlide())
        ->slides([blankSlide()])
        ->section('Coca-Cola')
        ->section('Google', [blankSlide()]);

    expect(sectionSizes($presentation))->toBe([
        'Ford' => 2,
        'Coca-Cola' => 0,
        'Google' => 1,
    ]);
})->skip(! Presentation::supportsSections(), 'Needs a PHPPresentation with sections.');

it('writes the sections into the deck', function () {
    Storage::fake('local');

    $path = Presentation::make('Test', branding: Branding::class)
        ->slides([blankSlide()])
        ->section('Johnson & Johnson', [blankSlide(), blankSlide()])
        ->create()
        ->save('sections');

    $zip = new ZipArchive;
    $zip->open($path);
    $xml = $zip->getFromName('ppt/presentation.xml');
    $zip->close();

    $dom = new DOMDocument;
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('p14', 'http://schemas.microsoft.com/office/powerpoint/2010/main');

    $sections = collect(iterator_to_array($xpath->query('//p14:sectionLst/p14:section')))
        ->mapWithKeys(fn (DOMElement $section) => [
            $section->getAttribute('name') => collect(iterator_to_array($xpath->query('p14:sldIdLst/p14:sldId', $section)))
                ->map(fn (DOMElement $slide) => $slide->getAttribute('id'))
                ->all(),
        ])
        ->all();

    expect($sections)->toBe([
        'Default Section' => ['256'],
        'Johnson & Johnson' => ['257', '258'],
    ]);
})->skip(! Presentation::supportsSections(), 'Needs a PHPPresentation with sections.');

it('adds the slides without sections when PHPPresentation has none', function () {
    $presentation = Presentation::make('Test', branding: Branding::class)
        ->slides([blankSlide()])
        ->section('Ford', [blankSlide(), blankSlide()])
        ->addSlide(blankSlide());

    expect($presentation->document->getSlideCount())->toBe(4);
})->skip(Presentation::supportsSections(), 'Needs a PHPPresentation without sections.');

