<?php

namespace BernskioldMedia\LaravelPpt\Concerns\Slides;

use PhpOffice\PhpPresentation\Shape\Hyperlink;

trait WithUrl
{
    protected ?string $url = null;

    protected ?int $slideNumberAnchor = null;

    protected ?bool $useTextColorForLink = null;

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function linkToSlide(int $slideNumber): static
    {
        $this->slideNumberAnchor = $slideNumber;

        return $this;
    }

    public function useTextColorForLink(bool $useTextColor): static
    {
        $this->useTextColorForLink = $useTextColor;

        return $this;
    }

    public function getLinkAsHyperlink(): ?Hyperlink
    {
        if ($this->url) {
            return (new Hyperlink($this->url))->setIsTextColorUsed($this->useTextColorForLink);
        }

        if ($this->slideNumberAnchor) {
            return (new Hyperlink)->setSlideNumber($this->slideNumberAnchor)->setIsTextColorUsed($this->useTextColorForLink);
        }

        return null;
    }

    /**
     * Data schema for URL/link properties.
     */
    protected static function dataSchemaWithUrl(): array
    {
        return [
            'properties' => [
                'url' => [
                    'type' => 'string',
                    'description' => 'URL to link to',
                    'format' => 'uri',
                ],
                'slideNumberAnchor' => [
                    'type' => 'integer',
                    'description' => 'Slide number to link to (internal link)',
                    'minimum' => 1,
                ],
            ],
            'required' => [],
        ];
    }
}
