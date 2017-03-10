<?php
namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use RevisionableTrait;

    protected $table = 'test_models';
    protected $guarded = [];

    protected $revisionFormattedFields = [
        'birth_date' => 'datetime:m/d/Y',
        'status' => 'boolean:inactive|active',
        'gender' => 'multiple:m,Male|f,Female',
    ];
}