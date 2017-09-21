<?php

namespace Convenia\Revisionable\Test\Revision;

use Convenia\Revisionable\Test\TestCase;
use Illuminate\Support\Collection;
use DB;
use Convenia\Revisionable\Test\Son;
use Convenia\Revisionable\Test\Father;
use Convenia\Revisionable\Test\AdoptiveParent;

class RevisionTest extends TestCase
{
	protected 
		$son,
		$father,
		$adoptiveParent;
		
	public function setUp()
	{
		parent::setUp();
		$this->father = Father::create(['name' => 'Father']);
		$this->adoptiveParent = AdoptiveParent::create(['name' => 'Adoptive Parent']);
	}

	public function test_revision_regular_relationship()
	{
		DB::table('sons')->truncate();
		$son = Son::create(['name' => 'Test Son']);
		$son->father_id = $this->father->id;
		$son->save();
		$revision = $son->revisionHistory()->first();
		$this->assertEquals($revision->newValue(), $this->father->name)	;
	}

    public function test_revision_regular_relationship_with_hours()
    {
        DB::table('sons')->truncate();
        $son = Son::create(['name' => 'Test Son']);
        $son->father_id = $this->father->id;
        $son->save();
        $revision = $son->revisionHistoryHours();
        $revision->first()->created_at = '2012-09-21 19:46:37';
        $revision->first()->save();
        $this->assertEquals($son->revisionHistoryHours()->count(), 0);

    }

    public function test_revision_divergent_relationship()
	{
		DB::table('sons')->truncate();
		$son = Son::create(['name' => 'Test Son']);
		$son->adoptive_id = $this->adoptiveParent->id;
		$son->save();
		$revision = $son->revisionHistory()->first();
		$this->assertEquals($revision->newValue(), $this->adoptiveParent->name);
	}
}