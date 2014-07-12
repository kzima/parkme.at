<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Location table migration.
 */
class LocationMigration 
{
    function run()
    {
        Capsule::schema()->dropIfExists('locations');
        
        Capsule::schema()->create('locations', function($table) {
            $table->increments('id')->unsigned();
            $table->string('type', 20);                                     // Onstreet / carpark
            $table->smallInteger('meter_number')->unsigned();
            $table->string('street');
            $table->string('suburb');
            $table->string('locality');                                     // Local description
            $table->tinyInteger('maximum_stay')->unsigned();
            $table->smallInteger('vehicle_bays')->unsigned();
            $table->smallInteger('motorcycle_bays')->unsigned();
            $table->decimal('vehicle_peak_rate', 6, 2)->unsigned();
            $table->decimal('vehicle_off_peak_rate', 6, 2)->unsigned();
            $table->decimal('motorcycle_rate', 6, 2)->unsigned();
            $table->tinyInteger('tariff_zone')->unsigned();;
            $table->string('restrictions')->nullable();
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->timestamps();
        });
    }
}