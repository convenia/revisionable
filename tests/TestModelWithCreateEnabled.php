<?php
namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class TestModelWithCreateEnabled extends Model
{
    use RevisionableTrait;

    protected $revisionCreationsEnabled = true;
    
    protected $table = 'test_models';
    protected $guarded = [];
}