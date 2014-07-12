<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Unparked table migration.
 */
class UnparkedMigration 
{
    function run()
    {
        Capsule::schema()->dropIfExists('unparked');
        
        Capsule::schema()->create('unparked', function($table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned();
            $table->string('vehicle');
            $table->timestamps();
            $table->index('location_id');
        });
    }
}