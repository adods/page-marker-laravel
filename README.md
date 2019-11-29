# Page Marker for Laravel

Extension from [`adods\page-marker`](https://github.com/adods/page-marker) for Laravel

## Installation

Via Composer:

```
composer require adods/page-marker-laravel
```

or just download the file manually and put in your lib directory

## Requirement

- [Laravel](https://laravel.com) 5.8 or 6.0
- [`adods\page-marker`](https://github.com/adods/page-marker)

## Usage Difference

`init()` now return `Illuminate\Http\RedirectResponse` when conditions are met.

```php
private $marker;

public function __construct(
    PageMarkerLaravel $marker
) {
    $this->marker = $marker;
}
```

```php
if ($redir = $this->marker->init()) {
    return $redir;
}
```
