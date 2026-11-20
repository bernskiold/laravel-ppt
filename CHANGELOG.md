# Changelog

All notable changes to `laravel-ppt` will be documented in this file.

## 1.1.1 - 2026-11-20

### What's Changed

* Bump body-parser from 1.20.6 to 1.20.9 in /docs by @dependabot[bot] in https://github.com/bernskiold/laravel-ppt/pull/88
* Bump postcss-selector-parser from 6.0.15 to 6.1.5 in /docs by @dependabot[bot] in https://github.com/bernskiold/laravel-ppt/pull/90
* Bump node-forge from 1.3.2 to 1.4.3 in /docs by @dependabot[bot] in https://github.com/bernskiold/laravel-ppt/pull/91
* Bump joi from 17.13.7 to 17.13.9 in /docs by @dependabot[bot] in https://github.com/bernskiold/laravel-ppt/pull/92
* Add PHP 8.5 and 8.6 support and drop Laravel 11 by @ErikBernskiold in https://github.com/bernskiold/laravel-ppt/pull/94
* Make PHP 8.6 a required part of the test matrix by @ErikBernskiold in https://github.com/bernskiold/laravel-ppt/pull/95

**Full Changelog**: https://github.com/bernskiold/laravel-ppt/compare/1.1.0...1.1.1

## 1.1.0 - 2026-10-24

### Added

- `Presentation::section()` groups slides into PowerPoint sections. Slides before the first section go into "Default Section". This needs a PHPPresentation with sections, such as our fork; `Presentation::supportsSections()` says whether yours has them, and with upstream the slides are added without sections. (#85, #86)
- `RichTextBox`: paragraphs made of runs, each with its own formatting, optionally bulleted. (#87)
- `Cell::parts()` writes a table cell as runs with their own formatting. (#87)
- `withoutBorders()` on table cells draws no line on a side with no border set. (#87)
- `italic()` on components with font settings, on text parts and on table cells. (#87)
- `Charts\Doughnut`, with a colour per slice and `holeSize()`. (#87)
- Per-series data label options on charts: `labelFormat`, `dataLabelBold` and `dataLabelSize`. (#87)

### Changed

- The position and size setters return `static`, so they chain on subclasses. (#87)
- Still requires `phpoffice/phppresentation` `^1.2.0`. The installation page explains how to use our fork instead. (#86)

### Fixed

- The chart docs named `showDataValues()` and `hideDataValues()`; the real methods are `withDataValues()` and `withoutDataValues()`. (#87)
