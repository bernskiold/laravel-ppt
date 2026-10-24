<?php

namespace BernskioldMedia\LaravelPpt\Presentation;

use BernskioldMedia\LaravelPpt\Branding\Branding;
use BernskioldMedia\LaravelPpt\Concerns\Makeable;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithPadding;
use BernskioldMedia\LaravelPpt\Concerns\Slides\WithSize;
use BernskioldMedia\LaravelPpt\Contracts\CustomizesPowerpointBranding;
use BernskioldMedia\LaravelPpt\Enums\WriterType;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Exception\OutOfBoundsException;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Section;
use PhpOffice\PhpPresentation\Writer\PDF\DomPDF;

use function config;
use function method_exists;

/**
 * @method static static make(string $title, ?int $width = null, ?int $height = null, ?string $branding = null)
 */
class Presentation
{
    use Conditionable,
        Makeable,
        Tappable,
        WithPadding,
        WithSize;

    /**
     * The document instance.
     */
    public PhpPresentation $document;

    /**
     * The user that we are creating the presentation as.
     */
    protected ?Authenticatable $user = null;

    /**
     * The default branding class for the presentation
     */
    public ?Branding $branding = null;

    /**
     * The writer type to use when saving the presentation.
     */
    protected WriterType $writerType = WriterType::PowerPoint2007;

    /**
     * Custom configuration callback for the writer.
     */
    protected ?Closure $writerCallback = null;

    /**
     * The section slides are added to, once one has been started.
     */
    protected ?Section $currentSection = null;

    /**
     * The name PowerPoint gives the section holding the slides before the first.
     */
    public const DEFAULT_SECTION = 'Default Section';

    public function __construct(
        protected string $title,
        ?int $width = null,
        ?int $height = null,
        ?string $branding = null
    ) {
        $this->user = auth()->user();

        // Set default sizes.
        $this->width = $width ?? config('powerpoint.defaults.presentation.width', 1280);
        $this->height = $height ?? config('powerpoint.defaults.presentation.height', 720);

        // Set default padding.
        $this->verticalPadding = config('powerpoint.defaults.presentation.verticalPadding', 0);
        $this->horizontalPadding = config('powerpoint.defaults.presentation.horizontalPadding', 0);

        // Create the presentation instance.
        $this->document = new PhpPresentation;

        // Remove the first slide which PHP-Presentation creates by default.
        try {
            $this->document->removeSlideByIndex(0);
        } catch (OutOfBoundsException) {
            // Do nothing.
        }

        // Set the default branding in three priority levels:
        // 1. Passed in as a parameter
        // 2. The user's branding class if it exists
        // 3. The default branding class from the config file
        if ($branding) {
            $this->branding($branding);
        } elseif ($this->user instanceof CustomizesPowerpointBranding && class_exists($this->user->powerpointBrandingClass())) {
            $this->branding($this->user->powerpointBrandingClass());
        } else {
            $this->branding(config('powerpoint.defaults.presentation.branding', Branding::class));
        }

        $this->boot();
    }

    protected function boot(): void
    {
        // Override in child classes.
    }

    /**
     * Save everything to the document and create the presentation.
     */
    public function create(): static
    {

        // Set the size.
        $this->document->getLayout()
            ->setCX($this->width, DocumentLayout::UNIT_PIXEL)
            ->setCY($this->height, DocumentLayout::UNIT_PIXEL);

        $this->saveProperties();

        return $this;
    }

    /**
     * Save the presentation to a file on a disk.
     */
    public function save(string $filename, ?string $disk = null, bool $inRootFolder = false): string
    {
        if (! $disk) {
            $disk = config('powerpoint.output.disk', 'local');
        }

        // Get the file extension based on the writer type
        $extension = $this->writerType->extension();

        if (! $inRootFolder) {
            $directory = config('powerpoint.output.directory', 'ppt');
            Storage::disk($disk)->makeDirectory($directory);

            $path = "$directory/$filename.$extension";
        } else {
            $path = "$filename.$extension";
        }

        $path = Storage::disk($disk)->path($path);

        // Create the writer with the specified type
        $writer = IOFactory::createWriter($this->document, $this->writerType->value);

        // Auto-configure PDF writer with DomPDF adapter
        if ($this->writerType === WriterType::PDF && empty($this->writerCallback)) {
            $writer->setPDFAdapter(new DomPDF);
        }

        // Apply custom writer configuration if provided
        if ($this->writerCallback) {
            ($this->writerCallback)($writer);
        }

        $writer->save($path);

        return $path;
    }

    /**
     * The slides to add to the presentation.
     *
     * @param  array<BaseSlide>  $slides
     */
    public function slides(array $slides = []): static
    {
        foreach ($slides as $slide) {
            $this->addSlide($slide instanceof PresentationSlide ? $slide() : $slide);
        }

        return $this;
    }

    /**
     * Add a single slide to the presentation.
     */
    public function addSlide(BaseSlide $slide): static
    {
        $created = $slide->create($this);

        $this->currentSection?->addSlide($created);

        return $this;
    }

    /**
     * Start a section, as PowerPoint shows them in its slide pane, and add any
     * slides given. Every slide added after it joins it until the next starts.
     *
     * PowerPoint keeps every slide in a section once a deck has any, so the
     * slides added before the first are put in a section of their own.
     *
     * @param  array<BaseSlide|PresentationSlide>  $slides
     */
    public function section(string $name, array $slides = []): static
    {
        if ($this->currentSection === null && $this->document->getSlideCount() > 0) {
            $this->document->createSection(static::DEFAULT_SECTION)
                ->setSlides($this->document->getAllSlides());
        }

        $this->currentSection = $this->document->createSection($name);

        return $this->slides($slides);
    }

    /**
     * Force the use of a specific branding for this presentation.
     */
    public function branding(string $brandingClass): static
    {
        $this->branding = $brandingClass::make();

        return $this;
    }

    /**
     * Create the presentation as a specific user.
     */
    public function forUser(Authenticatable $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the writer type to use when saving the presentation.
     */
    public function writer(WriterType $type): static
    {
        $this->writerType = $type;

        return $this;
    }

    /**
     * Get the current writer type.
     */
    public function getWriterType(): WriterType
    {
        return $this->writerType;
    }

    public function asPowerPoint(): static
    {
        return $this->writer(WriterType::PowerPoint2007);
    }

    public function asPdf(): static
    {
        return $this->writer(WriterType::PDF);
    }

    public function asHtml(): static
    {
        return $this->writer(WriterType::HTML);
    }

    /**
     * Configure the writer with a custom callback.
     * The callback receives the writer instance and can be used to set
     * writer-specific options like PDF adapters or ZIP adapters.
     */
    public function configureWriter(callable $callback): static
    {
        $this->writerCallback = $callback;

        return $this;
    }

    /**
     * Set various document properties to the document.
     */
    protected function saveProperties(): void
    {
        $this->document
            ->getDocumentProperties()
            ->setTitle($this->title)
            ->setCreator($this->branding->creatorCompanyName())
            ->setCompany($this->branding->creatorCompanyName())
            ->setLastModifiedBy($this->branding->creatorCompanyName());

        if ($this->user && method_exists($this->user, 'powerpointCreatorName')) {
            $this->document->getDocumentProperties()->setCreator($this->user->powerpointCreatorName());
        }

        if ($this->user && method_exists($this->user, 'powerpointCompanyName')) {
            $this->document->getDocumentProperties()->setCompany($this->user->powerpointCompanyName());
        }
    }
}
