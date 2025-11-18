<?php

namespace BernskioldMedia\LaravelPpt\Components\Charts;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithLegend;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithMarkerControls;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithXAxis;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithYAxis;
use PhpOffice\PhpPresentation\Shape\Chart\Marker;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Outline;

class Line extends ChartComponent
{
    use WithLegend,
        WithMarkerControls,
        WithXAxis,
        WithYAxis;

    public bool $smooth = false;

    protected function initializeChart(): void
    {
        $this->chart = new \PhpOffice\PhpPresentation\Shape\Chart\Type\Line;
        $this->withoutDataValues();
    }

    protected function chartTypeSeriesData(Series $series, int $index, array $seriesData): void
    {
        $seriesColor = $this->getSeriesColorFromData($seriesData, $index);

        $outline = (new Outline);
        $outline->setWidth(2);
        $outline->getFill()
            ->setStartColor($seriesColor)
            ->setFillType(Fill::FILL_SOLID)
            ->setEndColor($seriesColor);

        $series->setOutline($outline);

        if ($this->showMarker) {
            $series->getMarker()->setSymbol(Marker::SYMBOL_CIRCLE);
            $series->getMarker()->setSize(7);

            $series->getMarker()
                ->getBorder()
                ->setLineWidth(0);

            $series->getMarker()
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($seriesColor)
                ->setEndColor($seriesColor);
        } else {
            $series->getMarker()->setSize(0);
        }
    }

    public function smooth(bool $smooth = true): self
    {
        $this->smooth = $smooth;

        return $this;
    }

    public function render(): static
    {
        $this->chart->setIsSmooth($this->smooth);

        return $this;
    }
}
