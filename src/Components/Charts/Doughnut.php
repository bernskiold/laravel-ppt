<?php

namespace BernskioldMedia\LaravelPpt\Components\Charts;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithLegend;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithXAxis;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithYAxis;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Doughnut as DoughnutType;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

/**
 * A ring divided into slices — progress against a target, or a whole and its
 * parts.
 *
 * One series is what a doughnut draws. Each slice takes its colour from the
 * series' `colors`, by position, and falls back to the branding's chart colours
 * in the same order, so the key beside a chart can be coloured to match.
 *
 *     Doughnut::make([['label' => 'Progress', 'data' => ['Done' => 85, 'Left' => 15], 'colors' => ['ff7c6cf2', 'ffd9d9d9']]])
 *         ->holeSize(70)
 *         ->slide($slide)
 *         ->get();
 *
 * Values are hidden by default, since a doughnut is usually read with its
 * figure written in the hole; `withDataValues()` turns them back on. A ring has
 * no axes, so both are carried hidden for the chart frame to read.
 */
class Doughnut extends ChartComponent
{
    use WithLegend,
        WithXAxis,
        WithYAxis;

    protected int $holeSize = 50;

    protected function initializeChart(): void
    {
        $this->chart = new DoughnutType;
        $this->showDataValues = false;
        $this->showXAxis = false;
        $this->showYAxis = false;
        $this->yAxisMax = null;
    }

    /**
     * How much of the ring is hole, in per cent — 10 to 90.
     */
    public function holeSize(int $holeSize): static
    {
        $this->holeSize = max(10, min(90, $holeSize));

        return $this;
    }

    public function render(): static
    {
        $this->chart->setHoleSize($this->holeSize);

        return $this;
    }

    protected function chartTypeSeriesData(Series $series, int $index, array $seriesData): void
    {
        $colors = $seriesData['colors'] ?? [];

        foreach (array_values(array_keys($seriesData['data'])) as $position => $category) {
            $color = isset($colors[$position])
                ? new Color($colors[$position])
                : $this->slide->presentation->branding->chartColor($position);

            $series->getDataPointFill($position)
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($color);
        }
    }
}
