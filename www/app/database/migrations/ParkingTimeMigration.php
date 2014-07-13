<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Parking time table migration.
 */
class ParkingTimeMigration 
{
    function run()
    {
        Capsule::schema()->dropIfExists('parking_times');
        
        Capsule::schema()->create('parking_times', function($table) {
            $table->increments('id');
            $table->integer('parking_location_id')->unsigned();
            $table->string('start_day');
            $table->string('end_day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->index('parking_location_id');
        });
    }
}