<?php

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Components\Charts\Bar;
use BernskioldMedia\LaravelPpt\Components\Charts\Doughnut;
use BernskioldMedia\LaravelPpt\Components\ChartShape;
use BernskioldMedia\LaravelPpt\Components\RichTextBox;
use BernskioldMedia\LaravelPpt\Components\Table\Cell;
use BernskioldMedia\LaravelPpt\Components\Table\Row;
use BernskioldMedia\LaravelPpt\Components\Table\Table;
use BernskioldMedia\LaravelPpt\Components\TextPart;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use BernskioldMedia\LaravelPpt\Presentation\Presentation;
use PhpOffice\PhpPresentation\Shape\Chart as ChartShapeType;
use PhpOffice\PhpPresentation\Shape\Chart\Type\Doughnut as DoughnutType;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\Table as TableShape;
use PhpOffice\PhpPresentation\Style\Border;

/**
 * The components a deck of tables, rich bullets and two charts needs: runs
 * with formatting of their own in a cell and a text box, cells with no
 * border, a doughnut, and data labels with a number format.
 */
function slideFor(callable $render): BaseSlide
{
    $slide = new class($render) extends BaseSlide
    {
        public function __construct(private $draw) {}

        protected function render(): void
        {
            ($this->draw)($this);
        }
    };

    Presentation::make('Test', branding: Branding::class)->slides([$slide]);

    return $slide;
}

it('writes a cell as runs, each bold or italic on its own', function () {
    $slide = slideFor(fn (BaseSlide $slide) => Table::make($slide, 1, [
        Row::make([Cell::make()->parts(TextPart::make('Lisa Materazzo')->bold(), TextPart::make(', CMO')->italic())]),
    ])->width(400)->height(40)->render());

    /** @var TableShape $table */
    $table = collect($slide->raw()->getShapeCollection())->first(fn ($shape) => $shape instanceof TableShape);
    $runs = $table->getRow(0)->getCell(0)->getParagraph(0)->getRichTextElements();

    expect($runs)->toHaveCount(2)
        ->and($runs[0]->getText())->toBe('Lisa Materazzo')
        ->and($runs[0]->getFont()->isBold())->toBeTrue()
        ->and($runs[0]->getFont()->isItalic())->toBeFalse()
        ->and($runs[1]->getFont()->isItalic())->toBeTrue();
});

it('draws no border on a side nobody set one on, when asked', function () {
    $slide = slideFor(fn (BaseSlide $slide) => Table::make($slide, 1, [
        Row::make([Cell::make('Plain')->withoutBorders()->borderBottom('ff000050', 1)]),
    ])->width(400)->height(40)->render());

    $table = collect($slide->raw()->getShapeCollection())->first(fn ($shape) => $shape instanceof TableShape);
    $borders = $table->getRow(0)->getCell(0)->getBorders();

    expect($borders->getTop()->getLineStyle())->toBe(Border::LINE_NONE)
        ->and($borders->getBottom()->getLineStyle())->toBe(Border::LINE_SINGLE);
});

it('writes paragraphs of runs as a bulleted list', function () {
    $slide = slideFor(fn (BaseSlide $slide) => RichTextBox::make($slide, [
        [TextPart::make('Jim Farley')->bold(), TextPart::make(' – build the relationship')],
        'Second bullet',
    ])->bulleted()->size(10)->position(20, 20)->width(300)->height(100)->render());

    /** @var RichText $box */
    $box = collect($slide->raw()->getShapeCollection())->first(fn ($shape) => $shape instanceof RichText);

    expect($box->getParagraphs())->toHaveCount(2)
        ->and($box->getParagraph(0)->getRichTextElements()[0]->getFont()->isBold())->toBeTrue()
        ->and($box->getParagraph(0)->getBulletStyle()->getBulletChar())->toBe('•')
        ->and($box->getParagraph(1)->getPlainText())->toBe('Second bullet');
});

it('draws a doughnut with a colour for each slice and a hole of the size asked for', function () {
    $slide = slideFor(function (BaseSlide $slide): void {
        ChartShape::make($slide, Doughnut::make([['label' => 'Progress', 'data' => ['Done' => 85, 'Left' => 15], 'colors' => ['ff7c6cf2', 'ffd9d9d9']]])
            ->holeSize(70)
            ->slide($slide)
            ->get())
            ->position(20, 20)->width(200)->height(200)->render();
    });

    /** @var ChartShapeType $chart */
    $chart = collect($slide->raw()->getShapeCollection())->first(fn ($shape) => $shape instanceof ChartShapeType);
    $type = $chart->getPlotArea()->getType();
    $series = $type->getSeries()[0];

    expect($type)->toBeInstanceOf(DoughnutType::class)
        ->and($type->getHoleSize())->toBe(70)
        ->and($series->getDataPointFill(0)->getStartColor()->getARGB())->toBe('ff7c6cf2')
        ->and($series->getDataPointFill(1)->getStartColor()->getARGB())->toBe('ffd9d9d9')
        ->and($series->hasShowValue())->toBeFalse();
});

it('writes data labels in the number format and size a series asks for', function () {
    $slide = slideFor(function (BaseSlide $slide): void {
        ChartShape::make($slide, Bar::make([['label' => 'Net sales', 'data' => ['A' => 418.2, 'B' => 123.4], 'labelFormat' => '#,##0', 'dataLabelSize' => 9]])
            ->slide($slide)
            ->get())
            ->position(20, 20)->width(400)->height(200)->render();
    });

    $chart = collect($slide->raw()->getShapeCollection())->first(fn ($shape) => $shape instanceof ChartShapeType);
    $series = $chart->getPlotArea()->getType()->getSeries()[0];

    expect($series->getDlblNumFormat())->toBe('#,##0')
        ->and($series->getFont()->getSize())->toBe(9);
});
