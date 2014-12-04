# Janitor

[![Build Status](http://img.shields.io/travis/Anahkiasen/janitor.svg?style=flat)](https://travis-ci.org/Anahkiasen/janitor)
[![Latest Stable Version](http://img.shields.io/packagist/v/anahkiasen/janitor.svg?style=flat)](https://packagist.org/packages/anahkiasen/janitor)
[![Total Downloads](http://img.shields.io/packagist/dt/anahkiasen/janitor.svg?style=flat)](https://packagist.org/packages/anahkiasen/janitor)
[![Scrutinizer Quality Score](http://img.shields.io/scrutinizer/g/Anahkiasen/janitor.svg?style=flat)](https://scrutinizer-ci.com/g/Anahkiasen/janitor/)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/Anahkiasen/janitor.svg?style=flat)](https://scrutinizer-ci.com/g/Anahkiasen/janitor/)
[![Dependency Status](https://www.versioneye.com/user/projects/53f1c65f13bb0677b1000744/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53f1c65f13bb0677b1000744)
[![Support via Gittip](http://img.shields.io/gittip/Anahkiasen.svg?style=flat)](https://www.gittip.com/Anahkiasen/)

Janitor is a tool to help you remove unused code, routes, views and assets from your codebase.

## Install

Simply run the following command via Composer:

```bash
$ composer require anahkiasen/janitor --dev
```

Then add Janitor's service provider to your application's local providers:

```php
'Janitor\JanitorServiceProvider',
```

## Usage

### Command-line

You can see the various things Janitor can do by simply running:

```bash
$ php artisan list janitor
```

### Programmatically

Janitor can also be used programmatically, in order to do so:

```php
<?php
// Define your codebase
$codebase = new Janitor\Codebase('app');

// Create an instance of any of Janitor's analyzer classes
$analyzer = new Janitor\Services\Analyzers\ViewsAnalyzer($codebase);

// Tell it which files you wish to analyze, and run the process
$analyzer->setFiles('app/views');
$files = $analyzer->analyze();
```

Here, `$files` will be a Collection of instances of AbstractAnalyzedEntity.
Its most important property is the `usage` property, it's an integer whose value goes from 0 (file unused) to 1 (file used). The value can vary between these two points to indicate how certain Janitor is that the file is used.

```json
{
  "root": "/Users/foobar/Sites/foo/bar/app/views",
  "name": "_emails/feedback.twig",
  "usage": 0
}
```

### Available analyzers

| Name             | Description                                          | Status  |
| ---              | ---                                                  | ---     |
| ViewsAnalyzer    | Analyzes your codebase and check for unused views    |         |
| DatabaseAnalyzer | Checks your database for dead entries                | Planned |
| CodebaseAnalyzer | Checks your codebase for unused classes, models, etc | Planned |
| RoutesAnalyzer   | Checks your views and controllers for unused routes  | Planned |

Contributions and suggestions are welcome.

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/thephpleague/:package_name/blob/master/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License AbstractAnalyzedFile](LICENSE.md) for more information.
