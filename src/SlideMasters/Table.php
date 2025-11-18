<?php

namespace BernskioldMedia\LaravelPpt\SlideMasters;

use BernskioldMedia\LaravelPpt\Components\Table\Cell;
use BernskioldMedia\LaravelPpt\Components\Table\Row;
use BernskioldMedia\LaravelPpt\Components\Table\Table as TableComponent;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSlideTitle;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;

/**
 * @method static static make(string $title = '', array $headers = [], array $data = [])
 */
class Table extends BaseSlide implements DynamicallyCreatable
{
    use WithSlideTitle;

    public function __construct(
        string $title = '',
        protected array $headers = [],
        protected array $data = [],
    ) {
        $this->slideTitle = $title;
    }

    protected function render(): void
    {
        if (! empty($this->slideTitle)) {
            $titleBox = $this->renderTitle();
            $yOffset = $titleBox->height + 75;
        } else {
            $yOffset = 75;
        }

        if (empty($this->headers) || empty($this->data)) {
            return;
        }

        $columns = count($this->headers);
        $tableWidth = $this->presentation->width - (2 * $this->horizontalPadding);
        $columnWidth = (int) ($tableWidth / $columns);

        $rows = [];

        // Create header row
        $headerCells = [];
        foreach ($this->headers as $header) {
            $headerCells[] = Cell::make($header)
                ->width($columnWidth)
                ->bold()
                ->marginTop(2.5)
                ->marginBottom(2.5)
                ->alignLeft()
                ->alignMiddle();
        }

        $rows[] = Row::make($headerCells)->height(40);

        $rowIndex = 0;

        // Create data rows
        foreach ($this->data as $rowData) {
            $dataCells = [];
            $rowIndex++;
            foreach ($rowData as $cellData) {
                $cell = Cell::make((string) $cellData)
                    ->width($columnWidth)
                    ->alignLeft()
                    ->alignMiddle();

                if ($rowIndex === 1) {
                    $cell->borderTop($this->presentation->branding->slideTheme(static::class)->textColor ?? 'ff000000', 2);
                }

                $dataCells[] = $cell;
            }
            $rows[] = Row::make($dataCells)->height(35);
        }

        // Calculate table height
        $tableHeight = $this->presentation->height - $yOffset - 75;

        TableComponent::make($this, $columns, $rows)
            ->width($tableWidth)
            ->height($tableHeight)
            ->position($this->horizontalPadding, $yOffset)
            ->render();
    }

    public static function dataSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'The slide title',
                ],
                'headers' => [
                    'type' => 'array',
                    'description' => 'Array of table header labels',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
                'data' => [
                    'type' => 'array',
                    'description' => 'Array of table rows (each row is an array of cell values)',
                    'items' => [
                        'type' => 'array',
                        'items' => [
                            'type' => ['string', 'number'],
                        ],
                    ],
                ],
            ],
            'required' => ['title', 'headers', 'data'],
        ];
    }

    public static function description(): string
    {
        return 'A slide with a title and a formatted table with styling';
    }

    public static function exampleData(): array
    {
        return [
            'title' => 'Quarterly Results',
            'headers' => ['Quarter', 'Revenue', 'Growth'],
            'data' => [
                ['Q1 2024', '$2.5M', '15%'],
                ['Q2 2024', '$3.2M', '28%'],
                ['Q3 2024', '$3.8M', '19%'],
                ['Q4 2024', '$4.1M', '8%'],
            ],
        ];
    }
}
