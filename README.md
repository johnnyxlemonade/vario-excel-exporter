# Vario Excel Exporter

Lightweight PHP utility designed to analyze large Excel datasets and generate filtered exports in multiple formats with zero memory overhead.

## 🔗 Live Demo
You can try the application here: [https://vario-export.lemonadeframework.cz/](https://vario-export.lemonadeframework.cz/)

## 💡 Demonstration Highlights (The "How-To")

This project serves as a demonstration of how to handle common PHP bottlenecks and architectural challenges gracefully:

* **Clean Architecture & SRP**: The project is strictly divided into Application, Domain, and Infrastructure layers. The logic is orchestrated by a lean `ParameterProcessor`.
* **Request Object Pattern**: Input validation and state are encapsulated in a `ProcessRequest` object, decoupling the application logic from global HTTP state (`$_GET`).
* **Repository & Snapshotting**: The `ParameterRepository` abstracts the complexity of data persistence. It transparently handles **NDJSON snapshotting** to avoid re-parsing heavy Excel files.
* **Custom DI Container**: A "Pure DI" Container with strictly typed getters manages object lifecycles without reflection or "magic" overhead.
* **Streaming Pattern**: Data is pushed to `php://output` line-by-line. The `ProductFilterMapper` uses a `callable` writer, making the business logic completely format-agnostic.

## 🛡️ Quality Assurance

* **PHPStan Level 10**: The codebase is analyzed at the highest possible strictness level with `phpstan-strict-rules` and `bleedingEdge`.
* **Strict Coding Standard**: Enforced via **PHP-CS-Fixer** using the modern `@PER-CS2.0` ruleset and mandatory `strict_types`.
* **Zero Memory Leaks**: Designed to process millions of rows with a constant memory footprint.

## Core Features

* **Memory-Efficient Streaming**: Implements custom streams for **CSV**, **XLSX**, and **JSON**.
* **Constant Memory Usage**: Runs comfortably even with a **strict 8MB RAM limit** (as low as 4MB when using snapshots).
* **NDJSON Persistence**: High-performance, row-by-row data caching that stays memory-efficient even for huge datasets.
* **Modern PHP 8.1+**: Fully utilizes Enums, Readonly properties, Constructor Promotion, and Match expressions.

## Why "Streamed"?

Unlike standard export methods that build the entire file in memory before serving, this tool "pushes" data to the browser line-by-line.

This ensures that the server's memory consumption remains constant regardless of whether you are exporting 100 or 1,000,000 rows. **Theoretically, the system could stream petabytes of data with a 2MB PHP memory limit.**

## Why OpenSpout instead of PhpSpreadsheet?

For this specific use case, **OpenSpout** was chosen over the more common PhpSpreadsheet/PHPOffice library:

* **Memory Footprint**: PhpSpreadsheet stores the entire spreadsheet in memory as a collection of objects. OpenSpout reads and writes row-by-line, avoiding `Memory Limit Exhausted` errors.
* **Speed**: By bypassing complex cell styling and object mapping, OpenSpout processes files significantly faster.
* **Architecture Alignment**: Its native support for iterators perfectly aligns with the streaming architecture used in this project.

## Usage

1.  **Drop** your source file into the system.
2.  **Select** parameters to ignore or include via the UI.
3.  **Download** the result in your preferred format (**Excel**, **CSV**, or **JSON**) in real-time.

## 🛠️ Development

To maintain the high quality of the codebase, you can run:

```bash
# Run static analysis (PHPStan Level 10)
composer stan

# Check coding standard
vendor/bin/php-cs-fixer fix --dry-run --diff

# Automatically fix coding standard issues
vendor/bin/php-cs-fixer fix
