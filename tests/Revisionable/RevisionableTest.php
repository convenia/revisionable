<?php
namespace Convenia\Revisionable\Test\Revisionable;

use Carbon\Carbon;
use Convenia\Revisionable\Test\TestCase;
use Convenia\Revisionable\Test\TestModelWithCreateEnabled;
use DB;
use Illuminate\Support\Collection;
use Convenia\Revisionable\Test\TestModel;

class RevisionableTest extends TestCase
{
  
    public function test_revision_save_on_create()
    {
        DB::table('test_models')->truncate();

        $insertData = [
            'name' => 'New',
            'gender' => 'f'
        ];

        $model = TestModelWithCreateEnabled::create($insertData);

        $revisions = $model->revisionHistory;

        $revisions->each(function ($revision) use ($insertData) {
            $this->assertEquals($insertData[$revision->key], $revision->newValue());
            $this->assertEquals(null, $revision->oldValue());
        });

        $this->assertInstanceOf(Collection::class, $revisions);
    }
    
    public function test_revision_is_stored()
    {
        $this->testModel->name = 'Changed';
        $this->testModel->save();

        $revisions = $this->testModel->revisionHistory;

        $revisions->each(function ($revision) {
            $this->assertEquals('Changed', $revision->newValue());
            $this->assertEquals('test', $revision->oldValue());
        });

        $this->assertInstanceOf(Collection::class, $revisions);
    }

    public function test_revision_respect_limit()
    {
        $this->testModelWithLimit->name = 'Changed';
        $this->testModelWithLimit->save();

        $this->testModelWithLimit->name = 'Changed again';
        $this->testModelWithLimit->save();

        $revisions = $this->testModelWithLimit->revisionHistory;

        $this->assertCount(1, $revisions);
    }

    public function test_revision_is_disabled()
    {
        $this->testModelWithRevisionDisabled->name = 'Changed';
        $this->testModelWithRevisionDisabled->save();

        $revisions = $this->testModelWithRevisionDisabled->revisionHistory;

        $this->assertCount(0, $revisions);
    }

    public function test_revision_child()
    {
        $this->testModelWithParent->parent_id = $this->testModelParent->id;
        $this->testModelWithParent->save();

        $revisions = $this->testModelParent->revisionChildHistory();

        $this->assertCount(1, $revisions);
    }

    public function test_revision_child_with_hours_filter()
    {
        $this->testModelWithParent->parent_id = $this->testModelParent->id;
        $this->testModelWithParent->save();

        $revisions = $this->testModelParent->revisionChildHistoryHours();
        $revisions->first()->created_at = '2012-09-21 19:46:37';
        $revisions->first()->save();
        $revisions = $this->testModelParent->revisionChildHistoryHours();
        $this->assertCount(0, $revisions);
    }

    public function test_revision()
    {
        $this->testModel->name = 'Changed';
        $this->testModel->save();

        $revisions = $this->testModel->revisionHistory()->get();

        $this->assertCount(1, $revisions);
    }

    public function test_revision_with_hours()
    {
        $this->testModel->name = 'Changed';
        $this->testModel->save();

        $revisions = $this->testModel->revisionHistoryHours();
        $revisions->first()->created_at = '2012-09-21 19:46:37';
        $revisions->first()->save();
        $revisions = $this->testModel->revisionHistoryHours();
        $this->assertCount(0, $revisions);
    }
}