<?php

namespace BernskioldMedia\LaravelPpt\Contracts;

use BernskioldMedia\LaravelPpt\Components\ChartShape;

interface CustomizesShape
{
    public function shapeRender(ChartShape $shape): void;
}
