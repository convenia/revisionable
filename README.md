![logo](revisionable.png)

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

[![Packagist](https://img.shields.io/packagist/v/convenia/revisionable.svg)](https://packagist.org/packages/convenia/revisionable)
[![Build Status](https://semaphoreci.com/api/v1/edbizarro/revisionable/branches/master/badge.svg)](https://semaphoreci.com/edbizarro/revisionable) [![StyleCI](https://styleci.io/repos/83733995/shield?branch=master)](https://styleci.io/repos/83733995) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/e4ec883fea5d4973a30738c5a1fff1e3)](https://www.codacy.com/app/Convenia/revisionable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=convenia/revisionable&amp;utm_campaign=Badge_Grade) [![Codacy Badge](https://api.codacy.com/project/badge/Coverage/e4ec883fea5d4973a30738c5a1fff1e3)](https://www.codacy.com/app/Convenia/revisionable?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=convenia/revisionable&amp;utm_campaign=Badge_Coverage) [![Code Climate](https://codeclimate.com/github/convenia/revisionable/badges/gpa.svg)](https://codeclimate.com/github/convenia/revisionable) [![Packagist](https://img.shields.io/packagist/dm/convenia/revisionable.svg)](https://packagist.org/packages/convenia/revisionable)

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

## Docs

* [Implementation](#implementation)
  * [Soft deletes](#soft)
  * [Creation](#create)
* [Contributing](#contributing)


<a name="implementation"></a>
## Implementation

```php
namespace App;

use Convenia\Revisionable\RevisionableTrait;

class Article extends Eloquent {
  
    use RevisionableTrait;
}
```

If needed, you can disable the revisioning by setting `$revisionEnabled` to false in your model. This can be handy if you want to temporarily disable revisioning, or if you want to create your own base model that extends revisionable, which all of your models extend, but you want to turn revisionable off for certain models.

```php
namespace App;

use Convenia\Revisionable\RevisionableTrait;

class Article extends Eloquent {
  
    use RevisionableTrait;
    
    protected $revisionEnabled = false;
}
```

You can also disable revisioning after X many revisions have been made by setting `$historyLimit` to the number of revisions you want to keep before stopping revisions.

```php
namespace App;

use Convenia\Revisionable\RevisionableTrait;

class Article extends Eloquent {
  
    use RevisionableTrait;
        
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
}
```
In order to maintain a limit on history, but instead of stopping tracking revisions if you want to remove old revisions, you can accommodate that feature by setting `$revisionCleanup`.

```php
namespace App;

use Convenia\Revisionable\RevisionableTrait;

class Article extends Eloquent {
  
    use RevisionableTrait;
            
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.
}
```
You can suspend or set the revision temporarily by calling the methods withourRevision() and withRevision(). 

```php

    Article::withoutRevision();
    $article = Article::create(['title' => 'Amazing Article']);
    $article->title = 'New amazing Article';
    $article->save();
    ...
    Article::withRevision();
    $article->body = 'Text body of an amazing article';
    $article->save();

```
However, this doesn't overrides the revisionEnabled variable. If you call the method withRevision() in a Model that has setted $revisionEnabled = false, the revision will not occur.

<a name="soft"></a>
### Divergent column and model names

Sometimes a model can have a relationship in which the column associated doesn't follow the eloquent pattern, being needed to specify the foreign. In these cases, you need to declare an array called divergentRelations, where the column name points to the model name, in lowercase. This makes possible to query the relationship field value (like name or title), when using the methods newValue or oldValue on the revision

```php

class Article extends Model
{
    public $divergentRelations = [ 
        'quoted_id' => 'quotedauthors',
    ]; 
    public function quotedAuthors() 
    {
        return $this->belongsTo(QuotedAuthors::class, 'quoted_id');
    }
}

class QuotedAuthor extends Model
{
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
    
    ...
    $newQuotedAuthor = QuotedAuthor::create(['name' => 'New Quoted Author']);
    $article->quoted_id = $newQuotedAuthor->id;
    $article->save();
    $revision = $article->revisionHistory()->first();
    $revision->newValue() = 'New Quoted Author';

```

If you don't set the array $divergentRelations and tries to get the revision newValue, you would get the id instead of the name or title;

```php

class Article extends Model
{
    public function quotedAuthors() 
    {
        return $this->belongsTo(QuotedAuthors::class, 'quoted_id');
    }
}

class QuotedAuthor extends Model
{
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
    
    ...
    $newQuotedAuthor = QuotedAuthor::create(['name' => 'New Quoted Author']);
    $article->quoted_id = $newQuotedAuthor->id;
    $article->save();
    $revision = $article->revisionHistory()->first();
    $revision->newValue() = 1 ;

```
<a name="soft"></a>
### Storing soft deletes

By default, if your model supports soft deletes, revisionable will store this and any restores as updates on the model.

You can choose to ignore deletes and restores by adding `deleted_at` to your `$dontKeepRevisionOf` array.

To better format the output for `deleted_at` entries, you can use the `isEmpty` formatter (see <a href="#format-output">Format output</a> for an example of this.)


<a name="create"></a>
### Storing creations

By default the creation of a new model is not stored as a revision.
Only subsequent changes to a model is stored.

If you want to store the creation as a revision you can override this behavior by setting `revisionCreationsEnabled` to `true` by adding the following to your model:
```php
protected $revisionCreationsEnabled = true;
```


<a name="contributing"></a>
## Contributing

Contributions are encouraged and welcome; to keep things organised, all bugs and requests should be
opened in the GitHub issues tab for the main project, at [convenia/revisionable/issues](https://github.com/convenia/revisionable/issues)
