<?php

namespace BernskioldMedia\LaravelPpt\Components\Table;

use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithBackgroundColor;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithFontSettings;

/**
 * @method static static make(array $cells = [])
 */
class Row
{
    use Makeable,
        WithBackgroundColor,
        WithFontSettings;

    public ?int $height = null;

    public function __construct(
        public array $cells = [],
    ) {}

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }
}
