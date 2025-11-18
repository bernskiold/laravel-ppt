<?php

namespace Bernskioldmedia\LaravelPpt\Enums;

enum WriterType: string
{
    case PowerPoint2007 = 'PowerPoint2007';
    case ODPresentation = 'ODPresentation';
    case PDF = 'PDF';
    case HTML = 'HTML';
    case Serialized = 'Serialized';

    /**
     * Get the file extension for this writer type.
     */
    public function extension(): string
    {
        return match ($this) {
            self::PowerPoint2007 => 'pptx',
            self::ODPresentation => 'odp',
            self::PDF => 'pdf',
            self::HTML => 'html',
            self::Serialized => 'phppt',
        };
    }
}
