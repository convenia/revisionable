<?php

namespace Convenia\Revisionable\Test;

use Convenia\Revisionable\RevisionableServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase.
 */
abstract class TestCase extends Orchestra
{
    /** @var \Convenia\Revisionable\Test\TestModel */
    protected $testModel;

    /** @var \Convenia\Revisionable\Test\TestModelWithLimit */
    protected $testModelWithLimit;

    /** @var \Convenia\Revisionable\Test\TestModelWithRevisionDisabled */
    protected $testModelWithRevisionDisabled;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDatabase($this->app);

        $this->testModel = TestModel::first();
        $this->testModelWithLimit = TestModelWithLimit::first();
        $this->testModelWithRevisionDisabled = TestModelWithRevisionDisabled::first();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RevisionableServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->timestamps();
        });

        TestModel::create(['name' => 'test']);

        include_once __DIR__.'/../database/migrations/create_revisions_table.php.stub';
        (new \CreateRevisionsTable())->up();
    }
}
