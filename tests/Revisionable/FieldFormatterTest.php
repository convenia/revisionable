<?php
namespace Convenia\Revisionable\Test\Revisionable;

use Carbon\Carbon;
use Convenia\Revisionable\Test\TestCase;
use Illuminate\Support\Collection;

class FieldFormatterTest extends TestCase
{
    public function test_date_format()
    {
        $this->testModel->birth_date = new Carbon('1985-08-01');
        $this->testModel->save();

        $revisions = $this->testModel->revisionHistory;

        $revisions->each(function ($revision) {
            $this->assertEquals('08/01/1985', $revision->newValue());
        });
    }
    
    public function test_boolean_format()
    {
        $this->testModel->status = 1;
        $this->testModel->save();

        $revisions = $this->testModel->revisionHistory;

        $revisions->each(function ($revision) {
            $this->assertEquals('active', $revision->newValue());
            $this->assertEquals('inactive', $revision->oldValue());
        });
    }

    public function test_multiple_format()
    {
        $this->testModel->gender = 'f';
        $this->testModel->save();

        $this->testModel->gender = 'm';
        $this->testModel->save();

        $revisions = $this->testModel->revisionHistory;

        $this->assertEquals('Male', $revisions->get(1)->newValue());
        $this->assertEquals('Female', $revisions->get(1)->oldValue());

    }

}