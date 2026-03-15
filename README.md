# Vario Excel Exporter

![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)
![PHPStan Level
10](https://img.shields.io/badge/PHPStan-level%2010-brightgreen.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Lightweight PHP utility designed to analyze large Excel datasets and
generate filtered exports in multiple formats using a streaming
architecture with constant memory usage.

------------------------------------------------------------------------

## Live Demo

You can try the application here:

https://vario-export.lemonadeframework.cz/

------------------------------------------------------------------------

## About this project
This project started as a **one-time data processing task**.

A client provided a large Excel dataset containing product parameters and their values.  
The goal was to analyze this dataset and determine whether it could be used to configure **category filters** in the target system (Seyfor / Vario).

From the dataset the tool extracts:

- potential **filter names** (product parameters)
- possible **filter values**
- relationships between **products, filters and values**

To help validate the data structure, the application generates two export datasets:

- **Filter definitions export** – list of filters and their possible values
- **Product mapping export** – mapping between products, filters and values

These exports can then be inspected or imported into the target system to verify that the filter structure works as expected.

The original Excel dataset used during development is included in this repository for demonstration purposes.

Although the application itself was built for a specific dataset, many parts of the codebase were intentionally designed to be **reusable and framework-agnostic**.

Several components can easily be reused in other PHP projects dealing with:

- large data exports
- streaming responses
- memory-efficient file processing
- lightweight architecture experiments

Reusable parts include:

- streaming export pipeline (`RowWriter` abstraction)
- CSV / TSV / JSON / XML / XLSX writers
- memory-safe file streaming via `php://output`
- NDJSON snapshot persistence
- strict PHPStan Level 10 compatible architecture
- simple pure-PHP dependency injection container

------------------------------------------------------------------------

## Demonstration Highlights

This repository demonstrates how common PHP performance and architecture
problems can be solved cleanly.

### Clean Architecture

The project is structured into clear layers:

-   **Application**
-   **Domain**
-   **Infrastructure**
-   **Presentation**

Responsibilities are strictly separated and orchestrated through a lean
`ParameterProcessor`.

### Request Object Pattern

Input validation and state are encapsulated in a request object instead
of accessing global state like `$_GET` directly.

This keeps application logic decoupled from HTTP.

### Repository + Snapshot Strategy

The `ParameterRepository` hides the complexity of working with large
datasets.

It transparently handles **NDJSON snapshot persistence**, allowing the
system to avoid expensive Excel re‑parsing when the same dataset is
analyzed repeatedly.

### Custom DI Container

A lightweight **Pure Dependency Injection Container** provides:

-   strictly typed getters
-   zero reflection
-   zero runtime magic
-   predictable object lifecycle

### Streaming Export Pipeline

Exporters push rows **directly to the output stream** instead of
building files in memory.

This keeps memory usage constant regardless of dataset size.

The exporter logic itself is format‑agnostic thanks to the `RowWriter`
abstraction.

------------------------------------------------------------------------

## Core Features

-   **Memory‑Efficient Streaming** for **CSV**, **TSV**, **JSON**,
    **XML**, and **XLSX**
-   **Constant Memory Usage** even for millions of rows
-   **NDJSON Snapshot Persistence** for fast repeated processing
-   **Streaming download responses**
-   **Strict PHPStan Level 10 compatibility**
-   **Modern PHP 8.1 features**
    -   Enums
    -   Readonly properties
    -   Constructor property promotion
    -   Match expressions

------------------------------------------------------------------------

## Why “Streaming”?

Typical export implementations build the entire output file in memory
before sending it to the browser.

This quickly causes:

-   memory exhaustion
-   slow responses
-   large temporary allocations

This project instead **streams rows directly to the client**.

Memory consumption stays constant whether exporting:

-   100 rows
-   100 000 rows
-   10 000 000 rows

In theory the system can stream extremely large datasets with only a few
megabytes of PHP memory.

------------------------------------------------------------------------

## Why OpenSpout instead of PhpSpreadsheet?

For this use case **OpenSpout** is a better fit than PhpSpreadsheet.

### Memory footprint

PhpSpreadsheet keeps the entire spreadsheet in memory as a graph of
objects.

OpenSpout processes files **row‑by‑row**, avoiding
`Memory Limit Exhausted` errors.

### Speed

Because OpenSpout skips complex styling and heavy object mapping, it is
significantly faster for raw data processing.

### Architecture compatibility

Its iterator‑based design integrates naturally with the streaming
architecture used in this project.

------------------------------------------------------------------------

## Usage

1.  Upload or select a source Excel dataset
2.  Inspect detected parameters
3.  Ignore parameters that should not become filters
4.  Download prepared exports in your preferred format

Supported formats:

-   Excel (.xlsx)
-   CSV
-   TSV
-   JSON
-   XML

------------------------------------------------------------------------

## Development

To maintain code quality the project uses strict static analysis and
coding standards.

Run the following commands:

``` bash
# Static analysis (PHPStan level 10)
composer stan

# Check coding standard
vendor/bin/php-cs-fixer fix --dry-run --diff

# Automatically fix coding standard issues
vendor/bin/php-cs-fixer fix
```

------------------------------------------------------------------------

## License

MIT License

Copyright (c) 2026 Jan Mudrák
