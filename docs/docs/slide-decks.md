---
sidebar_position: 4
---

# Slide Decks

There are two ways to create a slide deck. Which one you choose will likely depend on how many slides it will have and
how complex it will be.

You can either create the presentation in a single file, or you can load slides from multiple files using a special
invokable class. Both methods can be combined in the same presentation.

## Creating a Slide Deck

To create a presentation, you need to create a class that extends the `Presentation` class. This class will be the
entry point for your presentation.

You can use the included make command to create a new presentation class:

```bash
php artisan slide-deck:presentation MyPresentation
```

This will create a new folder and class in the `app/Presentations/SlideDecks` directory:

```php
<?php

namespace App\Presentations\SlideDecks\MyPresentation;

use BernskioldMedia\LaravelPpt\Contracts\SlideDeck;
use BernskioldMedia\LaravelPpt\Presentation\Presentation;

class MyPresentation implements SlideDeck
{
    public function create(): Presentation
    {
        return Presentation::make(
            title: ''
        )
            ->slides([
                // Add slides here...
            ])
            ->create();
    }
}
```

You can now generate the presentation in your application by calling the class:

```php
$deck = MyPresentation::make()->create();
```

## Passing data to the presentation

You can pass data to the slide deck class by extending the slide deck class. The simplest way is to add a constructor
that accepts the data you want to pass:

```php
<?php

namespace App\Presentations\SlideDecks\MyPresentation;

use BernskioldMedia\LaravelPpt\Contracts\SlideDeck;

class MyPresentation extends SlideDeck
{

    public function __construct(
        protected array $data = []
    )
    {
    }

    public function create(): Presentation
    {
        return Presentation::make(
            title: ''
        )
            ->slides([
                // Add slides here...
            ])
            ->create();
    }
}
```

You can now pass data to the slide deck when you create it:

```php
$deck = MyPresentation::make(['name' => 'John Doe'])->create();
```

## Storing the slide deck

You can store the slide deck as a .pptx file on the server by calling the `save()` method on the slide deck:

```php
$deck = MyPresentation::make()
    ->create()
    ->save(filename: 'my-presentation');
```

By default, the file will be stored on the default disk defined in the package configuration, which defaults to
the `local` disk. You can change this on save by passing the disk name as the second parameter:

```php
$deck = MyPresentation::make()
    ->create()
    ->save(filename: 'my-presentation', disk: 'public');
```

By default, the file will be stored in a subfolder called `ppt` in the root of the disk. You can customize the directory
name by modifying the `output.directory` configuration value.

You can also disable storing in a subfolder by setting the third parameter to `true`:

```php
$deck = MyPresentation::make()
    ->create()
    ->save(
        filename: 'my-presentation',
        disk: 'local',
        inRootFolder: true
    );
```

## Adding Slides

You can add slides to the presentation by calling the `slides()` method on the presentation. This method accepts an
array of slides.

You can also add individual slides by calling the `addSlide()` method on the presentation, which accepts a single slide.

### From slide masters

You can add slides from slide masters by passing the slide master class to the `slides()` method:

```php
<?php

namespace App\Presentations\SlideDecks\MyPresentation;

use BernskioldMedia\LaravelPpt\Contracts\SlideDeck;use BernskioldMedia\LaravelPpt\SlideMasters\Title;

class MyPresentation extends SlideDeck
{
    public function create(): Presentation
    {
        return Presentation::make(
            title: 'My Presentation'
        )
            ->slides([
                Title::make('My Title'),
            ])
            ->create();
    }
}
```

### From slide classes

A presentation class can sometimes grow quite large and complex. To keep things organized, you can create separate
classes for each slide and then load them into the presentation.

To create a slide class, you can use the included make command:

```bash
php artisan make:slide MyPresentation MySlide
```

This will create a new class in the `app/Presentations/SlideDecks/MyPresentation/Slides` directory:

```php
<?php

namespace App\Presentations\SlideDecks\MyPresentation\Slides;

use BernskioldMedia\LaravelPpt\Presentation\PresentationSlide;
use BernskioldMedia\LaravelPpt\SlideMasters\Blank;

class MySlide implements PresentationSlide
{
    public function __invoke(): BaseSlide
    {
        return Blank::make(function(Blank $slide) {
            // Add slide content here...
        });
    }
}
```

You can now load the slide into the presentation:

```php
<?php

namespace App\Presentations\SlideDecks\MyPresentation;

use BernskioldMedia\LaravelPpt\Contracts\SlideDeck;
use App\Presentations\SlideDecks\MyPresentation\Slides\MySlide;

class MyPresentation extends SlideDeck
{
    public function create(): Presentation
    {
        return Presentation::make(
            title: 'My Presentation'
        )
            ->slides([
                MySlide::make('My Title'),
            ])
            ->create();
    }
}
```

:::tip Mix and match!
You can mix and match slide masters and slide classes in the same presentation. It can be helpful to keep simpler slides
using slide masters right in the slides array, and then move more complex slides to their own classes.
:::

## Sections

PowerPoint can group slides into named sections in its slide pane. Start one with `section()`, passing the slides it holds:

```php
return Presentation::make(
    title: 'Quarterly Review'
)
    ->slides([
        Title::make('Q3 Review'),
    ])
    ->section('Ford', [
        Title::make('Ford'),
        BulletPoints::make('Highlights', ['Revenue up', 'Two new markets']),
    ])
    ->section('Coca-Cola', [
        Title::make('Coca-Cola'),
    ])
    ->create();
```

A section runs until the next one starts, so any slide added after `section()`, through `slides()` or `addSlide()`, joins it. PowerPoint keeps every slide in a section once a deck has any, so slides added before the first section are put in one called "Default Section", as PowerPoint names it.

Sections are written to PowerPoint files only. A PDF or HTML export has none.
