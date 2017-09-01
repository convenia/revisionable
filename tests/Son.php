<?php

namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Convenia\Revisionable\Test\Father;
use Convenia\Revisionable\Test\AdoptiveParent;
use Illuminate\Database\Eloquent\Model;

/**
* Son
*/
class Son extends Model
{
	use RevisionableTrait;
	
	public $divergentRelations = [
		'adoptive_id' => 'adoptiveparent'
	];
	
	protected $table = 'sons';
	
	protected $guarded = [];
	
	public function father()
	{
		return $this->belongsTo(Father::class);
	}
	
	public function adoptiveParent()
	{
		return $this->belongsTo(AdoptiveParent::class, 'adoptive_id');
	}
}