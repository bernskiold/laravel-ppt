<?php

namespace BernskioldMedia\LaravelPpt\Components\Charts;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithLegend;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithXAxis;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithYAxis;
use PhpOffice\PhpPresentation\Shape\Chart\Marker;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

class Scatter extends ChartComponent
{
    use WithLegend,
        WithXAxis,
        WithYAxis;

    protected function initializeChart(): void
    {
        $this->chart = new \PhpOffice\PhpPresentation\Shape\Chart\Type\Scatter;
    }

    protected function chartTypeSeriesData(Series $series, int $index, array $seriesData): void
    {
        if ($this->slide->chartAxisColor) {
            $axisColor = new Color($this->slide->chartAxisColor);
        } else {
            $axisColor = $this->slide->presentation->branding->chartColor($index);
        }

        $series->getFont()
            ->setName($this->slide->presentation->branding->baseFont())
            ->setColor($axisColor)
            ->setBold(true);

        $series->getMarker()->setSymbol(Marker::SYMBOL_CIRCLE);
        $series->getMarker()->setSize(10);

        $series->getMarker()
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor($this->slide->presentation->branding->chartColor($index))
            ->setEndColor($this->slide->presentation->branding->chartColor($index));
    }

    public function render(): static
    {
        return $this;
    }
}
