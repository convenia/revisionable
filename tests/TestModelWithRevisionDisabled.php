<?php
namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class TestModelWithRevisionDisabled extends Model
{
    use RevisionableTrait;

    protected $table = 'test_models';
    protected $guarded = [];

    protected $revisionEnabled = false;
}