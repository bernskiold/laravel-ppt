<?php

namespace BernskioldMedia\LaravelPpt;

use BernskioldMedia\LaravelPpt\Commands\CreateNewSlideCommand;
use BernskioldMedia\LaravelPpt\Commands\CreateNewSlideDeckCommand;
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
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPptServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-ppt')
            ->hasConfigFile('powerpoint')
            ->hasConsoleCommands([
                CreateNewSlideDeckCommand::class,
                CreateNewSlideCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        // Register package's built-in slide masters
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
    }
}
