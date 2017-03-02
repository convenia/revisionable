<?php
namespace Convenia\Revisionable\Test\Revisionable;

use Carbon\Carbon;
use Convenia\Revisionable\Test\TestCase;
use Illuminate\Support\Collection;

class RevisionableTest extends TestCase
{
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

    public function test_formatted_revision()
    {
        $this->testModelWithRevisionDisabled->birth_date = new Carbon('1985-08-01');
        $this->testModelWithRevisionDisabled->save();

        $revisions = $this->testModelWithRevisionDisabled->revisionHistory;

        $this->assertCount(0, $revisions);
    }

}