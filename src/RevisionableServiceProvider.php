<?php

namespace Convenia\Revisionable;

use Illuminate\Support\ServiceProvider;

/**
 * Class RevisionableServiceProvider.
 */
class RevisionableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        foreach ($this->getMigrations() as $migration) {
            $this->publishes([
                __DIR__.'/../database/migrations/'.$migration.'.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_'.$migration.'.php'),
            ], 'migrations');
        }
    }

    protected function getMigrations()
    {
        return [
            'create_revisions_table',
        ];
    }
}
