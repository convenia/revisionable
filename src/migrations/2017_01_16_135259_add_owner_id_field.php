<?php

use Illuminate\Database\Migrations\Migration;

class addOwnerIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('revisions', function ($table) {
            $table->integer('owner_id')->nullAble();
            $table->integer('aggregate_id')->nullAble();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('revisions', function (Blueprint $table) {
            $table->dropColumn('owner_id');
            $table->dropColumn('aggregate_id');
        });
    }
}