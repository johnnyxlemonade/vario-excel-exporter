# Vario Excel Exporter

Lightweight PHP utility designed to analyze large Excel datasets and generate filtered exports in multiple formats with zero memory overhead.

## 🔗 Live Demo
You can try the application here: [https://vario-export.lemonadeframework.cz/](https://vario-export.lemonadeframework.cz/)

## 💡 Demonstration Highlights (The "How-To")

This project serves as a demonstration of how to handle common PHP bottlenecks and architectural challenges gracefully:

* **Custom DI Container**: Implementation of Dependency Injection and object lifecycle management without the overhead of a heavy framework.
* **Separation of Concerns**: The `ProductFilterMapper` (business logic) is completely decoupled from the transport layer. By using a `callable` writer, it supports any format (XLSX, CSV, JSON) without modifying the core logic.
* **Streaming Pattern**: A shift from the "generate-save-link" approach to direct `php://output` streaming. This is a best-practice for memory management in data-heavy enterprise applications.
* **Lazy Loading & Snapshotting**: Effective use of a JSON-based snapshot mechanism to avoid re-parsing heavy Excel files on every request, significantly improving UX and performance.

## Core Features

* **Memory-Efficient Streaming**: Implements custom streams for **CSV**, **XLSX**, and **JSON**.
* **Constant Memory Usage**: Designed to process large files with minimal RAM (runs comfortably even with an 8MB limit).
* **Dynamic Filtering**: Real-time parameter analysis and filtering before export.
* **Snapshot System**: JSON-based caching for datasets and analyzed parameters to speed up repeated requests.
* **Clean Architecture**: Built with a custom dependency injection container and clear separation of concerns.

## Tech Stack

* **PHP 8.x**: Utilizing modern features like `match` expressions, constructor promotion, and strict typing.
* **OpenSpout 4.x**: High-performance streaming library for XLSX/CSV processing.
* **Bootstrap 5 + FontAwesome 4.7**: For a clean, responsive user interface.

## Why "Streamed"?

Unlike standard export methods that build the entire file in memory (or temporary files) before serving, this tool "pushes" data to the browser line-by-line. This ensures that the server's memory consumption remains constant regardless of whether you are exporting 100 or 100,000 rows.

## Why OpenSpout instead of PhpSpreadsheet?

For this specific use case, **OpenSpout** was chosen over the more common PhpSpreadsheet/PHPOffice library for several reasons:

* **Memory Footprint**: PhpSpreadsheet stores the entire spreadsheet in memory as a collection of objects. For large datasets, this leads to `Memory Limit Exhausted` errors. OpenSpout reads and writes row-by-line, keeping memory usage minimal and constant.
* **Speed**: By avoiding the overhead of complex cell styling and object mapping, OpenSpout processes files significantly faster, which is critical for real-time exports.
* **Architecture Alignment**: The library's native support for iterators perfectly aligns with the streaming architecture and callback patterns used in this project.

## Usage

1. **Drop** your source file into the system.
2. **Select** which parameters to ignore or include via the UI.
3. **Download** the result in your preferred format (**Excel**, **CSV**, or **JSON**).
