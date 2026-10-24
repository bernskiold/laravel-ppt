---
sidebar_position: 7
---

# Text

There are two types of text components that can be used on a slide. The standard text box, as well as a multi-text box
that supports multiple paragraphs.

:::note Default component appearance options
All components have a series of default appearance options that can be set. You can find the full list of options in the
component [Overview](/components/intro) section.
:::

## Adding a text box

```php
use BernskioldMedia\LaravelPpt\Components\TextBox;
use BernskioldMedia\LaravelPpt\SlideMasters\Blank;

Blank::make(function (Blank $slide) {
    TextBox::make($slide, 'This is a text box')->render();
});
```

When creating a text box you can also pass a paragraph style to be used for formatting:

```php
TextBox::make($slide, 'This is a text box')
    ->paragraphStyle('heading-1')
    ->render();
```

## Adding a multi-text box

The multi-text box supports multiple instances of texts. Each text should be an instance of `TextPart`.

```php
use BernskioldMedia\LaravelPpt\Components\MultiTextBox;
use BernskioldMedia\LaravelPpt\SlideMasters\Blank;

Blank::make(function (Blank $slide) {
    MultiTextBox::make($slide, [
        TextPart::make('This is the first paragraph'),
        TextPart::make('This is the second paragraph'),
    ])->render();
});
```

## Adding a rich text box

The rich text box holds paragraphs in which each run can have its own formatting, such as a bold name ahead of plain
text. A paragraph is a string or a list of `TextPart`s. Each run falls back to the box's size, colour and font.

```php
use BernskioldMedia\LaravelPpt\Components\RichTextBox;
use BernskioldMedia\LaravelPpt\Components\TextPart;
use BernskioldMedia\LaravelPpt\SlideMasters\Blank;

Blank::make(function (Blank $slide) {
    RichTextBox::make($slide, [
        [TextPart::make('Lisa Materazzo')->bold(), TextPart::make(', CMO, left the company.')],
        'Pete Zillig is on leave.',
    ])
        ->bulleted()
        ->size(10)
        ->position(40, 120)
        ->width(400)
        ->height(200)
        ->render();
});
```

The rich text box has these methods in addition to the ones below:

- `bulleted(bool $bulleted = true, string $character = '•')`: Set the paragraphs as a bulleted list.
- `bulletColor(string $color)`: Set the colour of the bullets (in ARGB format).
- `spacingAfter(int $points)`: Set the space after each paragraph (in points).

## Customizing text boxes

Both text boxes and multi-text boxes support the following methods:

### Alignment

- `alignLeft()`: Align the text horizontally to the left.
- `alignCenter()`: Align the text horizontally to the center.
- `alignRight()`: Align the text horizontally to the right.
- `alignTop()`: Align the text vertically to the top.
- `alignMiddle()`: Align the text vertically to the middle.
- `alignBottom()`: Align the text vertically to the bottom.

### Spacing

- `margin(float $top, float? $right = null, float? $bottom = null, float? $left = null)`: Set the margin (in pixels).
- `marginTop(float $margin)`: Set the top margin (in pixels).
- `marginRight(float $margin)`: Set the right margin (in pixels).
- `marginBottom(float $margin)`: Set the bottom margin (in pixels).
- `marginLeft(float $margin)`: Set the left margin (in pixels).

### Background

- `backgroundColor(string $color)`: Set the background color (in ARGB format).

### Text

- `size(int $size)`: Set the font size (in pixels).
- `bold(bool $bold = true)`: Set the font weight to bold.
- `italic(bool $italic = true)`: Set the text to italic.
- `color(string $color)`: Set the font color (in ARGB format).
- `uppercase(bool $uppercase = true)`: Set the text to uppercase.
- `underlined(bool $underlined = true)`: Set the text to underlined.
- `letterSpacing(int $spacing)`: Set the letter spacing (in pixels).
- `font(string $font)`: Set the font family.
- `lineHeight(int $height)`: Set the line height (in pixels).

### Rotation

- `rotate(int $degrees)`: Rotate the text box (in degrees).

### Links

- `url(string $url)` - Set a URL to open when the shape is clicked.
- `linkToSlide(int $slideNumber)` - Set a slide number to link to when the shape is clicked.
- `useTextColorForLink(bool $use)` - Use the text color for the link instead of the default hyperlink color.

## Text Parts

Text parts are used in the multi-text box, the rich text box and table cells. They can be used to set the text, as well some formatting options.

```php
TextPart::make('This is the first paragraph')
    ->bold()
    ->size(20);
```

The following methods are available on text parts:

- `size(int $size)`: Set the font size (in pixels).
- `bold(bool $bold = true)`: Set the font weight to bold.
- `italic(bool $italic = true)`: Set the text to italic.
- `color(string $color)`: Set the font color (in ARGB format).
- `uppercase(bool $uppercase = true)`: Set the text to uppercase.
- `underlined(bool $underlined = true)`: Set the text to underlined.
- `letterSpacing(int $spacing)`: Set the letter spacing (in pixels).
- `font(string $font)`: Set the font family.
- `lineHeight(int $height)`: Set the line height (in pixels).
- `url(string $url)` - Set a URL to open when the shape is clicked.
- `linkToSlide(int $slideNumber)` - Set a slide number to link to when the shape is clicked.
- `useTextColorForLink(bool $use)` - Use the text color for the link instead of the default hyperlink color.
