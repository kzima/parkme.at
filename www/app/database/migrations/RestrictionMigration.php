<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Restriction table migration.
 */
class RestrictionMigration 
{
    function run()
    {
        Capsule::schema()->dropIfExists('restrictions');
        
        Capsule::schema()->create('restrictions', function($table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned();
            $table->string('start_day');
            $table->string('end_day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->index('location_id');
        });
    }
}