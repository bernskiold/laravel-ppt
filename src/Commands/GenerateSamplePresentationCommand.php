<?php

namespace BernskioldMedia\LaravelPpt\Commands;

use Bernskioldmedia\LaravelPpt\Enums\WriterType;
use BernskioldMedia\LaravelPpt\Registries\Brandings;
use BernskioldMedia\LaravelPpt\Registries\SlideMasters;
use BernskioldMedia\LaravelPpt\Support\PresentationFactory;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpPresentation\IOFactory;
use function base_path;
use function class_exists;
use function is_dir;
use function mkdir;
use function storage_path;

class GenerateSamplePresentationCommand extends Command
{
    public $signature = 'ppt:generate-sample {branding? : The branding class name or label to use}';

    public $description = 'Generates a sample presentation with all registered slide masters using their example data';

    public function handle(): int
    {
        // Determine the branding to use
        $brandingInput = $this->argument('branding');
        $branding = $this->resolveBranding($brandingInput);

        if (! $branding) {
            $this->error('No branding specified and no brandings registered.');
            $this->info('Please register at least one branding or specify a branding class name.');

            return self::FAILURE;
        }

        $this->info("Using branding: {$branding}");

        // Get all registered slide masters
        $masters = SlideMasters::all();

        if (empty($masters)) {
            $this->error('No slide masters are registered.');

            return self::FAILURE;
        }

        $this->info('Found '.count($masters).' registered slide masters.');

        // Build slides array from all masters
        $slides = [];
        foreach ($masters as $label => $masterData) {
            if (! isset($masterData['example']) || ! isset($masterData['class'])) {
                $this->warn("Skipping {$label} - missing example data or class");

                continue;
            }

            $slides[] = [
                'master' => $masterData['class'],
                'data' => $masterData['example'],
            ];

            $this->line("  - Added slide: {$label}");
        }

        if (empty($slides)) {
            $this->error('No slides could be generated from registered masters.');

            return self::FAILURE;
        }

        // Determine save directory and filename
        $directory = storage_path('tmp');
        File::ensureDirectoryExists($directory);

        $filename = 'sample-presentation-'.date('Y-m-d-His');

        $this->info('Generating presentation with '.count($slides).' slides...');

        // Create the presentation
        try {
            $presentation = PresentationFactory::create(
                title: 'Sample Presentation - '.date('Y-m-d H:i:s'),
                branding: $branding,
                slides: $slides
            );

            // Build the presentation
            $presentation->create();

            // Save directly to file system
            $writer = IOFactory::createWriter($presentation->document, WriterType::PowerPoint2007->value);
            $filePath = $directory.'/'.$filename.'.pptx';
            $writer->save($filePath);

            $this->info("✓ Sample presentation saved to: {$filePath}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generating presentation: '.$e->getMessage());
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        }
    }

    /**
     * Resolve the branding to use.
     */
    protected function resolveBranding(?string $input): ?string
    {
        // If input provided, try to use it
        if ($input) {
            // Check if it's a class name
            if (class_exists($input)) {
                return $input;
            }

            // Try to resolve from registry by label
            if (Brandings::exists($input)) {
                return Brandings::getClass($input);
            }

            $this->warn("Branding '{$input}' not found, using first registered branding...");
        }

        // Use first registered branding
        $allBrandings = Brandings::all();

        if (! empty($allBrandings)) {
            return array_values($allBrandings)[0];
        }

        return null;
    }
}
