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

}