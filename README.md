# Laravel revisionable

![logo](laravel-revisionable.png)

---

Easily create a revision history for any Eloquent model

```php
namespace App;

use Convenia\Revisionable\RevisionableTrait;

class Article extends Eloquent {
  
    use RevisionableTrait;
}
```

And you're good to go!

---

This project is a fork of https://github.com/VentureCraft/revisionable with some improvements and new features

The [v1 readme](README_v1.md) is also available if you want to use an old 1.x version 

---

We have badges! 
[![Build Status](https://travis-ci.org/convenia/revisionable.svg?branch=master)](https://travis-ci.org/convenia/revisionable) [![StyleCI](https://styleci.io/repos/83733995/shield?branch=master)](https://styleci.io/repos/83733995) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/e4ec883fea5d4973a30738c5a1fff1e3)](https://www.codacy.com/app/Convenia/revisionable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=convenia/revisionable&amp;utm_campaign=Badge_Grade) [![Code Climate](https://codeclimate.com/github/convenia/revisionable/badges/gpa.svg)](https://codeclimate.com/github/convenia/revisionable)

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