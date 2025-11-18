<?php

namespace BernskioldMedia\LaravelPpt\Registries;

use BernskioldMedia\LaravelPpt\SlideMasters\Blank;
use BernskioldMedia\LaravelPpt\SlideMasters\BlankWithTitle;
use BernskioldMedia\LaravelPpt\SlideMasters\BlankWithTitleSubtitle;
use BernskioldMedia\LaravelPpt\SlideMasters\BulletPoints;
use BernskioldMedia\LaravelPpt\SlideMasters\Chart;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartSquare;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartText;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartTitle;
use BernskioldMedia\LaravelPpt\SlideMasters\ChartTitles;
use BernskioldMedia\LaravelPpt\SlideMasters\FourUp;
use BernskioldMedia\LaravelPpt\SlideMasters\SixUp;
use BernskioldMedia\LaravelPpt\SlideMasters\Text;
use BernskioldMedia\LaravelPpt\SlideMasters\Title;
use BernskioldMedia\LaravelPpt\SlideMasters\TitleSubtitle;
use BernskioldMedia\LaravelPpt\SlideMasters\TwoUp;

use function class_basename;
use function collect;
use function interface_exists;
use function is_subclass_of;
use function method_exists;

class MasterRegistry
{
    /**
     * Application-registered slide masters via service provider.
     *
     * Applications can register custom masters in their service provider:
     * MasterRegistry::register([CustomMaster::class]);
     *
     * @var array<class-string>
     */
    public static array $appMasters = [];

    /**
     * Package's built-in slide masters.
     *
     * These are automatically available to all applications using the package.
     *
     * @var array<class-string>
     */
    protected static array $packageMasters = [
        Blank::class,
        BlankWithTitle::class,
        BlankWithTitleSubtitle::class,
        BulletPoints::class,
        Chart::class,
        ChartSquare::class,
        ChartText::class,
        ChartTitle::class,
        ChartTitles::class,
        FourUp::class,
        SixUp::class,
        Text::class,
        Title::class,
        TitleSubtitle::class,
        TwoUp::class,
    ];

    /**
     * Get all registered slide masters with their metadata.
     *
     * Returns masters that implement DynamicallyCreatable (if interface exists).
     * Each master includes: class, description, schema, example.
     *
     * @return array<string, array>
     */
    public static function all(): array
    {
        $allMasters = array_merge(
            static::$packageMasters,
            static::$appMasters
        );

        return collect($allMasters)
            ->filter(function ($class) {
                // Check if DynamicallyCreatable interface exists
                if (! interface_exists('BernskioldMedia\\LaravelPpt\\Contracts\\DynamicallyCreatable')) {
                    return true; // Return all if interface doesn't exist yet
                }

                return is_subclass_of($class, 'BernskioldMedia\\LaravelPpt\\Contracts\\DynamicallyCreatable');
            })
            ->mapWithKeys(fn ($class) => [
                class_basename($class) => static::buildMasterDefinition($class),
            ])
            ->all();
    }

    /**
     * Build master definition array with metadata.
     *
     * @param  class-string  $class
     */
    protected static function buildMasterDefinition(string $class): array
    {
        $definition = ['class' => $class];

        // Add schema info if available (from DynamicallyCreatable interface)
        if (method_exists($class, 'description')) {
            $definition['description'] = $class::description();
        }

        if (method_exists($class, 'dataSchema')) {
            $definition['schema'] = $class::dataSchema();
        }

        if (method_exists($class, 'exampleData')) {
            $definition['example'] = $class::exampleData();
        }

        return $definition;
    }

    /**
     * Get a specific slide master definition.
     */
    public static function get(string $masterName): ?array
    {
        return static::all()[$masterName] ?? null;
    }

    /**
     * Check if a slide master exists.
     */
    public static function exists(string $masterName): bool
    {
        return isset(static::all()[$masterName]);
    }

    /**
     * Get the class name for a slide master.
     *
     * @return class-string|null
     */
    public static function getClass(string $masterName): ?string
    {
        $master = static::get($masterName);

        return $master['class'] ?? null;
    }

    /**
     * Get available slide master names.
     *
     * @return array<string>
     */
    public static function names(): array
    {
        return array_keys(static::all());
    }

    /**
     * Register multiple slide masters at once.
     *
     * This is typically called in a service provider's boot() method:
     *
     * MasterRegistry::register([
     *     CustomMaster::class,
     *     AnotherMaster::class,
     * ]);
     *
     * @param  array<class-string>  $masters  Array of slide master class names
     */
    public static function register(array $masters): void
    {
        static::$appMasters = array_merge(
            static::$appMasters,
            $masters
        );
    }

    /**
     * Get all registered master classes (raw list without metadata).
     *
     * @return array<class-string>
     */
    public static function classes(): array
    {
        return array_merge(
            static::$packageMasters,
            static::$appMasters
        );
    }

    /**
     * Clear all registered application masters (primarily for testing).
     */
    public static function clear(): void
    {
        static::$appMasters = [];
    }
}
