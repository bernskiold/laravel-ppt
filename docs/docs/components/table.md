---
sidebar_position: 6
---

# Table

The table component is used to display data in a tabular format.

## Usage

```php
Blank::make(function(Blank $slide) {

    Table::make(
        columns: 2,
        rows: [
            Row::make([
                Cell::make('Cell Content'),
            ]),
]),
        ]
    )

});
```

:::note Default component appearance options
All components have a series of default appearance options that can be set. You can find the full list of options in the
component [Overview](/components/intro) section.
:::

## Customize the table text

There are fluent methods available to style the text in the table. These methods are chainable. The following methods
are available:

- `size(int $size)`: Set the font size (in pixels).
- `bold(bool $bold = true)`: Set the font weight to bold.
- `color(string $color)`: Set the font color (in ARGB format).
- `uppercase(bool $uppercase = true)`: Set the text to uppercase.
- `underlined(bool $underlined = true)`: Set the text to underlined.
- `letterSpacing(int $spacing)`: Set the letter spacing (in pixels).
- `font(string $font)`: Set the font family.
- `lineHeight(int $height)`: Set the line height (in pixels).

## Customize the row

There are fluent methods available to style the text in the row. These methods are chainable. The following methods
are available:

- `size(int $size)`: Set the font size (in pixels).
- `bold(bool $bold = true)`: Set the font weight to bold.
- `color(string $color)`: Set the font color (in ARGB format).
- `uppercase(bool $uppercase = true)`: Set the text to uppercase.
- `underlined(bool $underlined = true)`: Set the text to underlined.
- `letterSpacing(int $spacing)`: Set the letter spacing (in pixels).
- `font(string $font)`: Set the font family.
- `lineHeight(int $height)`: Set the line height (in pixels).

Additionally, you can customize the height and background color of the row:

- `height(int $height)`: Set the height of the row (in pixels).
- `backgroundColor(string $color)`: Set the background color of the row (in ARGB format).

## Customize the cell

You can have a cell span multiple columns or rows by using the fluent methods:

- `spansColumns(int $columns)`: Set the number of columns the cell should span.
- `spansRows(int $rows)`: Set the number of rows the cell should span.
- `spans(int $columns, int $rows = 0)`: Set the number of columns and rows the cell should span.

You can also set the width of the cell:

- `width(int $width)`: Set the width of the cell (in pixels).

There are fluent methods available to style the text in the cell. These methods are chainable. The following methods
are available:

- `size(int $size)`: Set the font size (in pixels).
- `bold(bool $bold = true)`: Set the font weight to bold.
- `italic(bool $italic = true)`: Set the text to italic.
- `color(string $color)`: Set the font color (in ARGB format).
- `uppercase(bool $uppercase = true)`: Set the text to uppercase.
- `underlined(bool $underlined = true)`: Set the text to underlined.
- `letterSpacing(int $spacing)`: Set the letter spacing (in pixels).
- `font(string $font)`: Set the font family.
- `lineHeight(int $height)`: Set the line height (in pixels).

To give a cell runs with their own formatting, such as a bold name ahead of plain text, pass
[text parts](/components/text#text-parts) in place of the cell's text. Each run falls back to the cell's, the row's and
the table's size, colour and font:

- `parts(TextPart ...$parts)`: Write the cell as these runs.

```php
Cell::make()->parts(
    TextPart::make('Budget')->bold(),
    TextPart::make(' pressure in some regions'),
);
```

A side with no border set still draws a hairline in PowerPoint. To draw no line there at all:

- `withoutBorders(bool $hide = true)`: Draw no border on any side that has no border set.

Additionally, you can link the cell:

- `url(string $url)` - Set a URL to open when the shape is clicked.
- `linkToSlide(int $slideNumber)` - Set a slide number to link to when the shape is clicked.
- `useTextColorForLink(bool $use)` - Use the text color for the link instead of the default hyperlink color.
