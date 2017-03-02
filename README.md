# Laravel revisionable
---

## Installation

#### Via [composer](http://getcomposer.org/doc/00-intro.md) (recommended)

```
composer require convenia/revisionable:^2.0
```

You can publish the migration with:

```
php artisan vendor:publish --provider="Convenia\Revisionable\RevisionableServiceProvider" --tag="migrations"
```

After the migration has been published you can create the revisions table by running the migrations:

```php artisan migrate```

## Contributing

Contributions are encouraged and welcome; to keep things organised, all bugs and requests should be
opened in the GitHub issues tab for the main project, at [convenia/revisionable/issues](https://github.com/convenia/revisionable/issues)