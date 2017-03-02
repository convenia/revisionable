<?php
namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class TestModelWithLimit extends Model
{
    use RevisionableTrait;

    protected $table = 'test_models';
    protected $guarded = [];

    protected $revisionEnabled = true;
    protected $historyLimit = 1; //Stop tracking revisions after 1 changes have been made.
}