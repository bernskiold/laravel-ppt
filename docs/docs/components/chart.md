---
sidebar_position: 3
---

# Chart

Chart components are used to create charts on slides. The package ships with the following chart components:

- Bar
- Column
- Line
- Radar
- Scatter

## Usage Overview

Charts are added to slides using chart shapes. The chart shape takes a chart component, which carries the data and
options. This example shows how to add a simple bar chart to a slide:

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Bar::make([
        'January' => 1,
        'February' => 2,
        'March' => 3,
    ]));
});
```

Each chart component contains a set of fluent customization methods (see below) that you can use to set various options.

The chart shape also has a set of fluent customization methods that you can use to set various options.

## Customizing the chart shape

The chart shape has a set of fluent customization methods that you can use to set various options.

- `title(string $title)` - Sets the chart title.
- `axisColor(string $color)` - Sets the color of the chart axis.
- `backgroundColor(string $color)` - Sets the background color of the chart.

:::note Default component appearance options
All components have a series of default appearance options that can be set. You can find the full list of options in the
component [Overview](/components/intro) section.
:::

## Bar Chart

The bar chart component is used to display data as a bar chart, where each data point is represented by a bar stemming
from a horizontal axis.

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Bar::make([
        'January' => 1,
        'February' => 2,
        'March' => 3,
    ]));
});
```

You can customize the bar chart using the following methods:

- `stacked()` - Sets the chart to be stacked, meaning series are stacked on top of each other in a single bar.
- `percentageStacked()` - Sets the chart to be stacked, meaning series are stacked on top of each other in a single bar,
  and each bar is scaled to 100%.

:::note Common component appearance methods
All chart components have a set of common methods that can be used to customize the appearance of the chart. You'll find
these at the end of this page.
:::

## Column Chart

The column chart component is used to display data as a column chart, where each data point is represented by a column
stemming from a vertical axis.

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Column::make([
        'January' => 1,
        'February' => 2,
        'March' => 3,
    ]));
});
```

The column chart has the same customization methods as the bar chart.

## Line Chart

The line chart component is used to display data as a line, where each data point is represented by dot joined together
with a line.

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Line::make([
        'January' => 1,
        'February' => 2,
        'March' => 3,
    ]));
});
```

You can customize the bar chart using the following methods:

- `smooth()` - Sets the chart to be smooth, meaning the line is smoothed to give a curved appearance.

:::note Common component appearance methods
All chart components have a set of common methods that can be used to customize the appearance of the chart. You'll find
these at the end of this page.
:::

## Radar Chart

The radar chart component is used to display data as a radar chart also known as a spider chart, where each data point
is represented by a line drawn around a central point, where values are represented by the distance from the center.

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Radar::make([
        'January' => 1,
        'February' => 2,
        'March' => 3,
    ]));
});
```

:::note Common component appearance methods
All chart components have a set of common methods that can be used to customize the appearance of the chart. You'll find
these at the end of this page.
:::

## Scatter Chart

The scatter chart component is used to display data as a scatter chart, where each data point is represented by a dot
drawn on a grid.

:::warning Unique X Value Requirement
PHP Presentation has a somewhat flawed way of drawing a scatter chart as it uses a key => value array for values. This
means that all X-axis values must be unique.
:::

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Radar::make([
        'January' => 1,
        'February' => 2,
        'March' => 3,
    ]));
});
```

:::note Common component appearance methods
All chart components have a set of common methods that can be used to customize the appearance of the chart. You'll find
these at the end of this page.
:::

## Doughnut Chart

The doughnut chart component draws a ring divided into slices: progress against a target, or a whole and its parts. It
draws one series. Each slice takes its colour from the series' `colors`, by position, and falls back to the branding's
chart colours in the same order.

```php
Blank::make(function(Blank $slide) {
    ChartShape::make($slide, Doughnut::make([
        [
            'label' => 'Progress',
            'data' => ['Done' => 85, 'Left' => 15],
            'colors' => ['ff7c6cf2', 'ffd9d9d9'],
        ],
    ])->holeSize(70));
});
```

You can customize the doughnut chart using the following methods:

- `holeSize(int $holeSize)` - Sets how much of the ring is hole, in per cent, from 10 to 90.

The values are hidden by default, because a doughnut is usually read with its figure written in the hole.
`withDataValues()` shows them again.

:::note Common component appearance methods
All chart components have a set of common methods that can be used to customize the appearance of the chart. You'll find
these at the end of this page.
:::

## Common Chart Customization Methods

The following methods are available on all chart components:

You can customize the bar chart using the following methods:

- `withDataLabels()` - Sets the chart to display labels for each data point
- `withoutDataLabels()` - Sets the chart to not display labels for each data point
- `withDataValues()` - Sets the chart to display the value of each data point
- `withoutDataValues()` - Sets the chart to not display the value of each data point
- `withLegend()` - Sets the chart to display a legend
- `withoutLegend()` - Sets the chart to not display a legend
- `xAxisTitle(string $title)` - Sets the title of the x-axis.
- `yAxisTitle(string $title)` - Sets the title of the y-axis.
- `withXAxis()` - Sets the chart to display the x-axis.
- `withoutXAxis()` - Sets the chart to not display the x-axis.
- `withYAxis()` - Sets the chart to display the y-axis.
- `withoutYAxis()` - Sets the chart to not display the y-axis.

### Data label options per series

When you give a chart its data as series, each series can also set how its data labels look:

- `labelFormat` - A number format for the labels, such as `'0.0'` or `'#,##0'`.
- `dataLabelBold` - Whether the labels are bold. Labels are bold by default.
- `dataLabelSize` - The font size of the labels.

```php
Column::make([
    ['label' => 'Revenue', 'data' => ['2025' => 12.4, '2026' => 13.1], 'labelFormat' => '0.0', 'dataLabelSize' => 9],
]);
```

