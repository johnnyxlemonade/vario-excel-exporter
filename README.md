# Vario Excel Exporter

Lightweight PHP utility designed to analyze large Excel datasets and generate filtered exports in multiple formats with zero memory overhead.

## 🔗 Live Demo
You can try the application here: [https://vario-export.lemonadeframework.cz/](https://vario-export.lemonadeframework.cz/)

## 💡 Demonstration Highlights (The "How-To")

This project serves as a demonstration of how to handle common PHP bottlenecks and architectural challenges gracefully:

* **Custom DI Container**: Implementation of a "Pure DI" Container with centralized factory definitions and strictly typed getters. No reflection, no magic, just clean object lifecycle management.
* **Separation of Concerns**: The `ProductFilterMapper` (business logic) is completely decoupled from the transport layer. By using a `callable` writer, it supports any format (XLSX, CSV, JSON) without modifying the core logic.
* **Streaming Pattern**: A shift from the "generate-save-link" approach to direct `php://output` streaming. This is a best-practice for memory management in data-heavy enterprise applications.
* **NDJSON Snapshotting**: Effective use of **Newline Delimited JSON** for snapshotting. This allows the system to avoid re-parsing heavy Excel files on every request while keeping memory usage at a minimum.

## 🛡️ Quality Assurance

* **PHPStan Level 10**: The codebase is analyzed at the highest possible strictness level.
* **Strict Rules**: Utilizing `phpstan-strict-rules` and `bleedingEdge` to ensure maximum type safety and reliability.
* **Zero Memory Leaks**: The architecture is designed to prevent memory accumulation during long-running streaming processes.
* **Strict Coding Standard**: Enforced via **PHP-CS-Fixer** using the modern `@PER-CS2.0` ruleset, including mandatory `declare(strict_types=1);` in all files to ensure consistent behavior and type safety.
* 
## Core Features

* **Memory-Efficient Streaming**: Implements custom streams for **CSV**, **XLSX**, and **JSON**.
* **Constant Memory Usage**: Runs comfortably even with a **strict 8MB RAM limit** (or as low as 4MB when reading from snapshots).
* **Dynamic Filtering**: Real-time parameter analysis and filtering before export.
* **NDJSON Snapshot System**: Row-by-row persistence for datasets to speed up repeated requests without loading everything into memory.
* **Clean Architecture**: Built with a custom dependency injection container and clear separation of concerns.

## Tech Stack

* **PHP 8.x**: Utilizing modern features like `match` expressions, constructor promotion, and strict typing.
* **OpenSpout 4.x**: High-performance streaming library for XLSX/CSV processing.
* **Bootstrap 5 + FontAwesome 4.7**: For a clean, responsive user interface.

## Why "Streamed"?

Unlike standard export methods that build the entire file in memory (or temporary files) before serving, this tool "pushes" data to the browser line-by-line.

This ensures that the server's memory consumption remains constant regardless of whether you are exporting 100 or 1,000,000 rows. **Theoretically, the downloader could stream petabytes of data with a 2MB PHP memory limit**, provided the client can consume the stream fast enough.

## Why OpenSpout instead of PhpSpreadsheet?

For this specific use case, **OpenSpout** was chosen over the more common PhpSpreadsheet/PHPOffice library:

* **Memory Footprint**: PhpSpreadsheet stores the entire spreadsheet in memory as a collection of objects. For large datasets, this leads to `Memory Limit Exhausted` errors. OpenSpout reads and writes row-by-line.
* **Speed**: By avoiding the overhead of complex cell styling and object mapping, OpenSpout processes files significantly faster.
* **Architecture Alignment**: The library's native support for iterators perfectly aligns with the streaming architecture and callback patterns used in this project.

## Usage

1.  **Drop** your source file into the system (see `export_vlastnosti_produktu.xlsx` for reference).
2.  **Select** which parameters to ignore or include via the UI.
3.  **Download** the result in your preferred format (**Excel**, **CSV**, or **JSON**) in real-time.

## 🛠️ Development

To maintain the high quality of the codebase, you can run the following commands:

```bash
# Run static analysis (PHPStan Level 10)
composer stan

# Check coding standard
vendor/bin/php-cs-fixer fix --dry-run --diff

# Automatically fix coding standard issues
vendor/bin/php-cs-fixer fix
