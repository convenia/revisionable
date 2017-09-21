<?php
namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class TestModelWithParent extends Model
{
    use RevisionableTrait;

    protected $revisionParent = 'parent';
    protected $table = 'test_models';
    protected $guarded = [];


    public function parent()
    {
        return $this->belongsTo(TestModelParent::class, 'parent_id');
    }
}