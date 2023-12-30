---
sidebar_position: 2
hide_table_of_contents: true
---

# Installation

You can install the package via composer:

```bash
composer require bernskioldmedia/laravel-ppt
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="BernskioldMedia\LaravelPpt\LaravelPptServiceProvider" --tag="laravel-ppt-config"
```

This is the contents of the published config file:

```php
return [
    'defaults' => [
        'presentation' => [
            'width' => 1280,
            'height' => 720,
            'verticalPadding' => 40,
            'horizontalPadding' => 40,
            'branding' => Branding::class,
            'dataSource' => '',
            'dataSourceApplication' => config('app.name', ''),
        ],
        'charts' => [
            'seriesColor' => 'ff000000',
        ],
        'masters' => [
            'slideTitleParagraphStyle' => 'slideTitle',
            'slideSubtitleParagraphStyle' => 'slideSubtitle',
            'bulletPointParagraphStyle' => 'bulletPoint',
            'nUpGridTitleParagraphStyle' => 'nUpGridTitle',
            'nUpGridBodyParagraphStyle' => 'nUpGridBody',
            'bodyParagraphStyle' => 'body',
            'sectionTitleParagraphStyle' => 'sectionTitle',
            'sectionSubtitleParagraphStyle' => 'sectionSubtitle',
        ],
    ],

    'baseBranding' => [
        'creatorCompanyName' => 'Bernskiold Media',
        'websiteUrl' => 'https://bernskioldmedia.com',
        'font' => 'Arial',
        'chartColors' => [
            'ff000000',
        ],
    ],

    'output' => [
        'disk' => 'local',
        'directory' => 'ppt',
    ],

    'paths' => [
        'slideDecks' => app_path('Presentations/SlideDecks'),
        'slideMasters' => app_path('Presentations/SlideMasters'),
        'base' => resource_path('presentations'),
        'branding' => resource_path('presentations/branding'),
    ],
];
```

