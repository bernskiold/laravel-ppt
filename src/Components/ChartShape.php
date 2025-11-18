<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Contracts\CustomizesShape;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Shape\Chart;
use PhpOffice\PhpPresentation\Shape\Chart\Gridlines;
use PhpOffice\PhpPresentation\Shape\Chart\Legend;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Line;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Radar;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

/**
 * @property Chart $shape
 *
 * @method static static make(BaseSlide $slide, ChartComponent $chartComponent, ?string $axisColor = Color::COLOR_BLACK, ?string $title = '')
 */
class ChartShape extends Component
{
    use WithBackgroundColor,
        WithShape;

    public function __construct(
        protected ChartComponent $chartComponent,
        protected ?string $axisColor = Color::COLOR_BLACK,
        protected ?string $title = '',
    ) {
        $this->backgroundColor = Color::COLOR_WHITE;
    }

    protected function initialize(): static
    {
        $this->shape = $this->slide
            ->raw()
            ->createChartShape()
            ->setName(str()->random());

        return $this;
    }

    public function title(string $title = ''): self
    {
        $this->title = $title;

        return $this;
    }

    public function axisColor(?string $color): self
    {
        $this->axisColor = $color;

        return $this;
    }

    protected function defaultHeight(): float
    {
        return 600;
    }

    protected function defaultWidth(): float
    {
        return 1200;
    }

    public function render(): static
    {
        $this->maybeDefaultDimensions();

        if ($this->slide->chartBackgroundColor) {
            $this->backgroundColor = $this->slide->chartBackgroundColor;
        }

        if ($this->slide->chartAxisColor) {
            $this->axisColor = $this->slide->chartAxisColor;
        }

        if (! empty($this->backgroundColor) && $this->backgroundColor !== 'transparent') {
            $this->shape->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color($this->backgroundColor));
        }

        // Set size and details.
        $this->shape->setResizeProportional(true)
            ->setHeight($this->height)
            ->setWidth($this->width)
            ->setOffsetX($this->x)
            ->setOffsetY($this->y);

        // Hide the chart title.
        $this->shape->getTitle()->setText($this->title);
        $this->shape->getTitle()->setVisible(! empty($this->title));
        $this->shape->getTitle()->setWidth(100);
        $this->shape->getTitle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set branding.
        $this->shape->getTitle()->getFont()->setName($this->slide->presentation->branding->baseFont())->setBold(true);
        $this->shape->getPlotArea()->getAxisY()->getFont()->setName($this->slide->presentation->branding->baseFont())->setBold(true);
        $this->shape->getPlotArea()->getAxisX()->getFont()->setName($this->slide->presentation->branding->baseFont())->setBold(true);

        if ($this->axisColor) {
            $this->shape->getPlotArea()
                ->getAxisY()
                ->getFont()
                ->setColor(new Color($this->axisColor))
                ->setName($this->slide->presentation->branding->baseFont());

            $this->shape->getPlotArea()
                ->getAxisY()
                ->getTickLabelFont()
                ->setColor(new Color($this->axisColor))
                ->setName($this->slide->presentation->branding->baseFont());

            $this->shape->getPlotArea()
                ->getAxisX()
                ->getFont()
                ->setColor(new Color($this->axisColor))
                ->setName($this->slide->presentation->branding->baseFont());

            $this->shape->getPlotArea()
                ->getAxisX()
                ->getTickLabelFont()
                ->setColor(new Color($this->axisColor))
                ->setName($this->slide->presentation->branding->baseFont());
        }

        // Place legends on top.
        $this->shape->getLegend()->setPosition(Legend::POSITION_BOTTOM);
        $this->shape->getLegend()->getBorder()->setLineStyle(Border::LINE_NONE);
        $this->shape->getLegend()->getFont()->setName($this->slide->presentation->branding->baseFont());
        $this->shape->getLegend()->getFont()->setColor(new Color($this->axisColor));

        // Add the chart to the area.
        $this->shape->getPlotArea()->setType($this->chartComponent->chart);

        // Set Axis Labels
        $this->shape->getPlotArea()
            ->getAxisX()
            ->setTitle(strtoupper($this->chartComponent->xAxisTitle))
            ->getFont()
            ->setCharacterSpacing(5)
            ->setName($this->slide->presentation->branding->baseFont());

        $this->shape->getPlotArea()
            ->getAxisY()
            ->setTitle(strtoupper($this->chartComponent->yAxisTitle))
            ->getFont()
            ->setCharacterSpacing(5)
            ->setName($this->slide->presentation->branding->baseFont());

        // Maybe show axes?
        $this->shape->getPlotArea()->getAxisX()->setIsVisible($this->chartComponent->showXAxis);
        $this->shape->getPlotArea()->getAxisY()->setIsVisible($this->chartComponent->showYAxis);

        // Max-min
        if (! $this->chartComponent->chart instanceof Line) {
            if ($this->chartComponent->yAxisMax) {
                $this->shape->getPlotArea()->getAxisY()->setMaxBounds($this->chartComponent->yAxisMax);
            }

            if ($this->chartComponent->yAxisMin) {
                $this->shape->getPlotArea()->getAxisY()->setMinBounds($this->chartComponent->yAxisMin);
            }
        }

        if ($this->chartComponent->chart instanceof Radar) {
            $oGridlines = new Gridlines;
            $oGridlines->getOutline()
                ->setWidth(1)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('ffdddddd'));
            $this->shape->getPlotArea()->getAxisY()
                ->setMajorGridlines($oGridlines)
                ->getOutline()
                ->setWidth(2)
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('ffaaaaaa'));
        }

        // Maybe show legend?
        $this->shape->getLegend()->setVisible($this->chartComponent->showLegend);

        if ($this->chartComponent instanceof CustomizesShape) {
            $this->chartComponent->shapeRender($this);
        }

        return $this;
    }
}
