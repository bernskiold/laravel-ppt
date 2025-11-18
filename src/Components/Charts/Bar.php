<?php

namespace BernskioldMedia\LaravelPpt\Components\Charts;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithLegend;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithXAxis;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithYAxis;

class Bar extends ChartComponent
{
    use WithLegend,
        WithXAxis,
        WithYAxis;

    protected bool $stacked = false;

    protected bool $percentageStacked = false;

    protected function initializeChart(): void
    {
        $this->chart = new \PhpOffice\PhpPresentation\Shape\Chart\Type\Bar;
    }

    public function stacked(bool $stacked = true): static
    {
        $this->stacked = $stacked;

        return $this;
    }

    public function percentageStacked(bool $stacked = true): static
    {
        $this->percentageStacked = $stacked;

        return $this;
    }

    public function render(): static
    {
        $this->chart->setGapWidthPercent(50);

        if ($this->stacked) {
            $this->chart->setBarGrouping(\PhpOffice\PhpPresentation\Shape\Chart\Type\Bar::GROUPING_STACKED);
        }

        if ($this->percentageStacked) {
            $this->chart->setBarGrouping(\PhpOffice\PhpPresentation\Shape\Chart\Type\Bar::GROUPING_PERCENTSTACKED);
        }

        return $this;
    }
}
