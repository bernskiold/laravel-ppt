<?php

namespace BernskioldMedia\LaravelPpt\Support;

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\Presentation;
use BernskioldMedia\LaravelPpt\Registries\Brandings;
use BernskioldMedia\LaravelPpt\Registries\SlideMasters;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PresentationFactory
{
    /**
     * Create a presentation from structured data.
     *
     * @param  string  $title  Presentation title
     * @param  string  $branding  Branding label (e.g. 'My Branding') from registry or fully qualified class name
     * @param  array  $slides  Array of slide configurations, each with 'master' (label or FQCN) and 'data' keys
     * @param  int|null  $width  Presentation width in pixels
     * @param  int|null  $height  Presentation height in pixels
     *
     * @throws InvalidArgumentException
     */
    public static function create(
        string $title,
        string $branding,
        array $slides,
        ?int $width = null,
        ?int $height = null
    ): Presentation {
        // Resolve branding: try registry first, then treat as class name
        $brandingClass = static::resolveBranding($branding);

        // Create presentation with branding
        $presentation = Presentation::make(
            title: $title,
            width: $width,
            height: $height,
            branding: $brandingClass
        );

        // Add each slide
        foreach ($slides as $index => $slideConfig) {
            static::addSlide($presentation, $slideConfig, $index);
        }

        return $presentation;
    }

    /**
     * Resolve a branding label or class to a fully qualified class name.
     *
     * @param  string  $branding  Branding label or fully qualified class name
     * @return class-string<Branding>
     *
     * @throws InvalidArgumentException
     */
    protected static function resolveBranding(string $branding): string
    {
        // Try to resolve from registry first
        if (Brandings::exists($branding)) {
            return Brandings::getClass($branding);
        }

        // Treat as fully qualified class name
        if (! class_exists($branding)) {
            throw new InvalidArgumentException(
                "Branding '{$branding}' not found in registry and class does not exist. ".
                'Available brandings: '.implode(', ', Brandings::names())
            );
        }

        if (! is_subclass_of($branding, Branding::class) && $branding !== Branding::class) {
            throw new InvalidArgumentException(
                "Branding class '{$branding}' must extend BernskioldMedia\\LaravelPpt\\Branding\\Branding."
            );
        }

        return $branding;
    }

    /**
     * Build and save a presentation, returning file information.
     *
     * @param  Presentation  $presentation  The presentation to save
     * @param  string|null  $filename  Custom filename (without extension), generates UUID if null
     * @param  string  $disk  Primary disk to save file to (default: 'local')
     * @param  string  $directory  Directory path on disk (default: 'ppt')
     * @param  bool  $inRootFolder  Save in root folder of disk (default: false)
     * @return array File information with keys: filename, path, disk
     */
    public static function buildAndSave(
        Presentation $presentation,
        ?string $filename = null,
        string $disk = 'local',
        string $directory = 'ppt',
        bool $inRootFolder = false
    ): array {
        $filename = $filename ?? (string) Str::uuid();

        // Build the presentation
        $presentation->create();

        // Determine the path
        if ($inRootFolder) {
            $relativePath = "{$filename}.pptx";
        } else {
            $relativePath = "{$directory}/{$filename}.pptx";
        }

        // Save using the Presentation's save method
        $absolutePath = $presentation->save($filename, $disk, $inRootFolder);

        return [
            'filename' => "{$filename}.pptx",
            'path' => $relativePath,
            'absolute_path' => $absolutePath,
            'disk' => $disk,
        ];
    }

    /**
     * Create and save a presentation in one step.
     *
     * @param  string  $branding  Branding name from BrandingRegistry or fully qualified class name
     * @param  array  $presentationOptions  Optional: width, height
     * @param  array  $saveOptions  Optional: filename, disk, directory, inRootFolder
     */
    public static function createAndSave(
        string $title,
        string $branding,
        array $slides,
        array $presentationOptions = [],
        array $saveOptions = []
    ): array {
        $presentation = static::create(
            title: $title,
            branding: $branding,
            slides: $slides,
            width: $presentationOptions['width'] ?? null,
            height: $presentationOptions['height'] ?? null
        );

        return static::buildAndSave(
            presentation: $presentation,
            filename: $saveOptions['filename'] ?? null,
            disk: $saveOptions['disk'] ?? 'local',
            directory: $saveOptions['directory'] ?? 'ppt',
            inRootFolder: $saveOptions['inRootFolder'] ?? false
        );
    }

    /**
     * Add a slide to the presentation using SlideFactory.
     *
     * @param  array  $slideConfig  Must contain 'master' (name or FQCN) and 'data' (array) keys
     * @param  int  $index  Slide index for error messages
     *
     * @throws InvalidArgumentException
     */
    protected static function addSlide(Presentation $presentation, array $slideConfig, int $index): void
    {
        // Validate slide configuration
        if (! isset($slideConfig['master'])) {
            throw new InvalidArgumentException(
                "Slide at index {$index} is missing required 'master' key."
            );
        }

        if (! isset($slideConfig['data'])) {
            throw new InvalidArgumentException(
                "Slide at index {$index} is missing required 'data' key."
            );
        }

        $master = $slideConfig['master'];
        $data = $slideConfig['data'];

        // Resolve master: try registry first, then treat as class name
        $masterClass = static::resolveMaster($master, $index);

        // Use SlideFactory to create the slide instance
        $slide = SlideFactory::create($masterClass, $data);

        // Add to presentation
        $presentation->addSlide($slide);
    }

    /**
     * Resolve a master label or class to a fully qualified class name.
     *
     * @param  string  $master  Master label (e.g. 'Blank With Title') or fully qualified class name
     * @param  int  $index  Slide index for error messages
     * @return class-string
     *
     * @throws InvalidArgumentException
     */
    protected static function resolveMaster(string $master, int $index): string
    {
        // Try to resolve from registry first
        if (SlideMasters::exists($master)) {
            return SlideMasters::getClass($master);
        }

        // Treat as fully qualified class name
        if (! class_exists($master)) {
            throw new InvalidArgumentException(
                "Slide master '{$master}' at index {$index} not found in registry and class does not exist. ".
                'Available masters: '.implode(', ', SlideMasters::names())
            );
        }

        // Validate master supports dynamic creation
        if (! is_subclass_of($master, DynamicallyCreatable::class)) {
            throw new InvalidArgumentException(
                "Slide master '{$master}' at index {$index} does not implement DynamicallyCreatable interface."
            );
        }

        return $master;
    }

    /**
     * Generate a temporary signed URL for a saved presentation file.
     *
     * This is a helper method for applications that need signed URLs.
     *
     * @param  string  $disk  The storage disk
     * @param  string  $path  The file path on the disk
     * @param  \DateTimeInterface|int  $expiration  Expiration time (default: 24 hours)
     * @return string The temporary URL
     */
    public static function generateSignedUrl(
        string $disk,
        string $path,
        \DateTimeInterface|int|null $expiration = null
    ): string {
        $expiration = $expiration ?? now()->addDay();

        return Storage::disk($disk)->temporaryUrl($path, $expiration);
    }
}
