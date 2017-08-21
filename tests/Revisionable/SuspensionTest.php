<?php

namespace Convenia\Revisionable\Test\Revisionable;

use Carbon\Carbon;
use Convenia\Revisionable\Test\TestCase;
use Illuminate\Support\Collection;
use Convenia\Revisionable\Test\TestModel;
use Convenia\Revisionable\Test\TestModelWithCreateEnabled;
use Convenia\Revisionable\Test\TestModelWithLimit;

class SuspensionTest extends TestCase
{
  
    public function test_suspension()
    {
        TestModel::suspendRevision();
        $model = TestModel::create(['name' => 'Test Name', 'Gender' => 'M']);
        $model->name = 'New Name';
        $model->save();
        
        $revisions = $model->revisionHistory;
        $this->assertCount(0, $revisions);
    }
    
    public function test_proceed_revision()
    {
        TestModel::proceedRevision();
        $model = TestModel::create(['name' => 'Test Name', 'Gender' => 'M']);
        $model->name = 'New Name';
        $model->save();
        
        $revisions = $model->revisionHistory;
        $this->assertCount(1, $revisions);
    }
    
    public function test_creation_enabled_suspension()
    {
        TestModelWithCreateEnabled::suspendRevision();
        $model = TestModel::create(['name' => 'Test Name', 'Gender' => 'M']);
        $revisions = $model->revisionHistory;
        $this->assertCount(0, $revisions);
    }
        
}