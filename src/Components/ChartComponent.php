<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Slides\ControlsDataVisibility;
use PhpOffice\PhpPresentation\Shape\Chart\Series;
use PhpOffice\PhpPresentation\Shape\Chart\Type\AbstractType;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

abstract class ChartComponent extends Component
{
    use ControlsDataVisibility;

    public AbstractType $chart;

    public function __construct(
        protected array $data
    ) {

        $this->initializeChart();
    }

    /**
     * @param  array  $data
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }

    protected function initializeChart(): void
    {
        // Extend this method to initialize the chart type.
    }

    public function modifyChart(callable $callback): static
    {
        $callback($this->chart);

        return $this;
    }

    public function get(): static
    {
        $this->render();
        $this->addSeriesToChart();

        return $this;
    }

    protected function addSeriesToChart(): void
    {
        foreach ($this->data as $index => $seriesData) {
            if (empty($seriesData['data'])) {
                continue;
            }

            $series = new Series($seriesData['label'], $seriesData['data']);
            $series->setShowSeriesName($this->showDataLabels);
            $series->setShowValue($this->showDataValues);

            $seriesColor = $this->getSeriesColorFromData($seriesData, $index);

            $series->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor($seriesColor);

            if (isset($seriesData['labelPosition'])) {
                $series->setLabelPosition($seriesData['labelPosition']);
            }

            $dataLabelColor = $seriesData['dataLabelColor'] ?? 'ffffffff';

            $series->getFont()
                ->setName($this->slide->presentation->branding->baseFont())
                ->setColor(new Color($dataLabelColor))
                ->setBold(true);

            $this->chartTypeSeriesData($series, $index, $seriesData);

            $this->chart->addSeries($series);
        }
    }

    protected function getSeriesColorFromData(array $seriesData, int $index): Color
    {
        if (isset($seriesData['color'])) {
            return new Color($seriesData['color']);
        }

        return $this->slide->presentation->branding->chartColor($index);
    }

    protected function chartTypeSeriesData(Series $series, int $index, array $seriesData): void
    {
        // Extend this method with any chart-specific series data methods.
    }
}
