<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;

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
        'creatorCompanyName' => config('app.name', ''),
        'websiteUrl' => config('app.url', ''),
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
