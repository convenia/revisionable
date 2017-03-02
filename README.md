![logo](laravel-revisionable.png)

[![Build Status](https://travis-ci.org/convenia/revisionable.svg?branch=master)](https://travis-ci.org/convenia/revisionable) [![StyleCI](https://styleci.io/repos/79227873/shield?branch=2.0)](https://styleci.io/repos/79227873) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/f9140cd44e2247958cfcd1a9045b799c)](https://www.codacy.com/app/Convenia/revisionable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=convenia/revisionable&amp;utm_campaign=Badge_Grade)
---

## Installation

#### Via [composer](http://getcomposer.org/doc/00-intro.md) (recommended)

```
composer require convenia/revisionable:^2.0
```

Next, you must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    Convenia\Revisionable\RevisionableServiceProvider::class,
];
```
You can publish the migration with:

```
php artisan vendor:publish --provider="Convenia\Revisionable\RevisionableServiceProvider" --tag="migrations"
```

After the migration has been published you can create the revisions table by running the migrations:

```
php artisan migrate
```

## Contributing

Contributions are encouraged and welcome; to keep things organised, all bugs and requests should be
opened in the GitHub issues tab for the main project, at [convenia/revisionable/issues](https://github.com/convenia/revisionable/issues)