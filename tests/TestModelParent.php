<?php
namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class TestModelParent extends Model
{
    use RevisionableTrait;

    protected $table = 'test_models';
    protected $guarded = [];

    public function child()
    {
        return $this->hasOne(TestModelParent::class, 'parent_id');
    }
}