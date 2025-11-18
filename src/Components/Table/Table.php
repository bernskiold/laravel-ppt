<?php

namespace BernskioldMedia\LaravelPpt\Components\Table;

use BernskioldMedia\LaravelPpt\Components\Component;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithFontSettings;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithShape;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;

/**
 * @method static static make(BaseSlide $slide, int $columns = 1, array $rows = [])
 */
class Table extends Component
{
    use WithFontSettings,
        WithShape;

    public function __construct(
        protected int $columns = 1,
        protected array $rows = [],
    ) {}

    protected function initialize(): static
    {
        $this->shape = $this->slide
            ->raw()
            ->createTableShape($this->columns)
            ->setName(str()->random());

        return $this;
    }

    public function render(): static
    {
        $this->shape
            ->setHeight($this->height)
            ->setWidth($this->width)
            ->setOffsetX($this->x)
            ->setOffsetY($this->y);

        foreach ($this->rows as $row) {

            $tableRow = $this->shape->createRow();

            if ($row->height) {
                $tableRow->setHeight($row->height);
            }

            if ($row->backgroundColor) {
                $tableRow->setFill($row->getBackgroundColorFill());
            }

            foreach ($row->cells as $cell) {
                $tableCell = $tableRow->nextCell();

                $tableCell->setColSpan($cell->colspan)
                    ->setRowSpan($cell->rowspan)
                    ->setBorders($cell->getBordersObject())
                    ->setWidth($cell->width);

                $fontSize = $cell->size ?? $row->size ?? $this->size ?? 14;
                $color = $cell->color ?? $row->color ?? $this->color ?? Color::COLOR_BLACK;
                $font = $cell->font ?? $row->font ?? $this->font ?? config('powerpoint.baseBranding.font');

                $tableCell->createTextRun($cell->text)
                    ->getFont()
                    ->setSize($fontSize)
                    ->setColor(new Color($color))
                    ->setBold($cell->bold)
                    ->setUnderline($cell->underlined ? Font::UNDERLINE_SINGLE : Font::UNDERLINE_NONE)
                    ->setName($font)
                    ->setCharacterSpacing($cell->letterSpacing);

                $tableCell->getActiveParagraph()
                    ->getAlignment()
                    ->setHorizontal($cell->horizontalAlignment)
                    ->setVertical($cell->verticalAlignment)
                    ->setMarginTop($cell->marginTop)
                    ->setMarginRight($cell->marginRight)
                    ->setMarginBottom($cell->marginBottom)
                    ->setMarginLeft($cell->marginLeft);

                if ($cell->backgroundColor) {
                    $tableCell->setFill($cell->getBackgroundColorFill());
                }

            }

        }

        return $this;
    }
}
