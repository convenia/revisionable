<?php 

namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Convenia\Revisionable\Test\Son;

/**
* Father
*/
class Father extends Model
{
	
	protected $table = 'fathers';
	
	protected $guarded = [];
	
	public function son()
	{
		return $this->hasMany(Son::class);
	}
}