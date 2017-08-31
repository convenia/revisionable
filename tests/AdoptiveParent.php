<?php

namespace Convenia\Revisionable\Test;

use Illuminate\Database\Eloquent\Model;
use Convenia\Revisionable\Test\Son;

/**
* AdoptiveParent
*/
class AdoptiveParent extends Model
{
	protected $table = 'adoptive_parents';
	
	protected $guarded = [];
		
	public function son()
	{
		return $this->hasMany(Son::class, 'adoptive_id');
	}
}