<?php

namespace BernskioldMedia\LaravelPpt\Components;

use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithFontSettings;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithUrl;
use BernskioldMedia\LaravelPpt\Contracts\GeneratesTextRun;
use PhpOffice\PhpPresentation\Shape\RichText\Run;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Font;

class TextPart implements GeneratesTextRun
{
    use Makeable,
        WithFontSettings,
        WithUrl;

    public function __construct(
        protected string $text = ''
    ) {}

    public function toTextRun(Component $component): Run
    {
        $underlined = $this->underlined ?? $component->underlined ?? false;

        $textRun = new Run($this->text);

        if ($this->url) {
            $textRun->setHyperlink($this->getLinkAsHyperlink());
        }

        $textRun->getFont()
            ->setSize($this->size ?? $component->size)
            ->setColor(new Color($this->color ?? $component->color))
            ->setBold($this->bold ?? $component->bold)
            ->setUnderline($underlined ? Font::UNDERLINE_SINGLE : Font::UNDERLINE_NONE)
            ->setName($this->font ?? $component->font ?? $component->slide->presentation->branding->baseFont())
            ->setCharacterSpacing($this->letterSpacing ?? $component->letterSpacing);

        return $textRun;
    }
}
