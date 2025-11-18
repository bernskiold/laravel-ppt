<?php

namespace BernskioldMedia\LaravelPpt\Support;

use BernskioldMedia\LaravelPpt\Components\ChartComponent;
use BernskioldMedia\LaravelPpt\Contracts\DynamicallyCreatable;
use BernskioldMedia\LaravelPpt\Presentation\BaseSlide;
use InvalidArgumentException;
use ReflectionClass;

class SlideFactory
{
    /**
     * Create a slide instance from a class name and data array.
     *
     * Uses reflection to automatically map data keys to constructor parameters.
     * Handles ChartComponent parameters using ChartFactory.
     *
     * @param  class-string<BaseSlide&DynamicallyCreatable>  $slideClass
     * @param  array  $data  Associative array of data matching constructor parameters
     *
     * @throws InvalidArgumentException
     */
    public static function create(string $slideClass, array $data): BaseSlide
    {
        if (! is_subclass_of($slideClass, DynamicallyCreatable::class)) {
            throw new InvalidArgumentException(
                "Slide class '{$slideClass}' does not implement DynamicallyCreatable interface."
            );
        }

        // Use reflection to get constructor parameters
        $reflection = new ReflectionClass($slideClass);
        $constructor = $reflection->getConstructor();
        $params = $constructor?->getParameters() ?? [];

        $args = [];
        foreach ($params as $param) {
            $paramName = $param->getName();
            $paramType = $param->getType()?->getName();

            // Special handling for ChartComponent parameters
            if ($paramType === ChartComponent::class || is_subclass_of($paramType, ChartComponent::class)) {
                // Handle multiple chart parameters using naming convention
                // e.g., leftChart -> leftChartType + leftChartData
                $chartTypeKey = $paramName.'Type';
                $chartDataKey = $paramName.'Data';

                if (isset($data[$chartTypeKey], $data[$chartDataKey])) {
                    // Multiple charts case (e.g., ChartTwoUp with leftChart, rightChart)
                    $args[] = ChartFactory::create($data[$chartTypeKey], $data[$chartDataKey]);
                } elseif (isset($data['chartType'], $data['chartData'])) {
                    // Single chart case (e.g., ChartTitle with chart parameter)
                    $args[] = ChartFactory::create($data['chartType'], $data['chartData']);
                } else {
                    throw new InvalidArgumentException(
                        "Missing chart data for parameter '{$paramName}'. ".
                        "Expected '{$chartTypeKey}' and '{$chartDataKey}' or 'chartType' and 'chartData'."
                    );
                }
            } else {
                // Map data key to constructor parameter, use default if not provided
                if (array_key_exists($paramName, $data)) {
                    $args[] = $data[$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new InvalidArgumentException(
                        "Missing required parameter '{$paramName}' for slide '{$slideClass}'."
                    );
                }
            }
        }

        return $reflection->newInstanceArgs($args);
    }
}
