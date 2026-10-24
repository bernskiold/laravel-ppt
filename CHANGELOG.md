# Changelog

All notable changes to `laravel-ppt` will be documented in this file.

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
